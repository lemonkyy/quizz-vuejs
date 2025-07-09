<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use App\Service\ValidateUsernameService;
use App\Service\ValidatePasswordService;

class RegisterController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, ValidateUsernameService $validateUsernameService, ValidatePasswordService $validatePasswordService)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['code' => 'ERR_MISSING_CREDENTIALS', 'error' => 'Missing email or password'], 400);
        }

        if (!$data['tosAgreedTo']) {
            return new JsonResponse(['code' => 'ERR_TOS_REFUSED', 'error' => 'TOS not agreed to'], 400);
        }

        $passwordValidation = $validatePasswordService->validate($data['password']);
        if ($passwordValidation) {
            return new JsonResponse($passwordValidation, 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['code' => 'ERR_INVALID_EMAIL', 'error' => 'Invalid email address'], 400);
        }

        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['code' => 'ERR_EMAIL_ALREADY_IN_USE', 'error' => 'Email already in use'], 400);
        }

        if (isset($data['username']) && is_string($data['username'])) {

            $username = $data['username'];
            $error = $validateUsernameService->validate($username);

            if ($error) {
                return new JsonResponse($error, 400);
            }

        } else {

            $maxAttempts = 25;
            $attempts = 0;

            do {
                $randomNumber = random_int(100000000, 999999999);
                $username = 'User#' . $randomNumber;
                $attempts++;
                $error = $validateUsernameService->validate($username);
                if ($attempts > $maxAttempts) {
                    return new JsonResponse(['code' => 'ERR_USERNAME_GENERATION_FAILED', 'error' => 'Could not generate unique username.'], 500);
                }

            } while ($error);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'User created.'], 201);
    }
}
