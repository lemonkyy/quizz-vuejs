<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\ValidateUsernameService;

class RegisterController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, ParameterBagInterface $params, ValidateUsernameService $validateUsernameService)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Invalid email address'], 400);
        }

        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already in use'], 400);
        }

        if (isset($data['username']) && is_string($data['username'])) {

            $username = $data['username'];
            $error = $validateUsernameService->validate($username);

            if ($error) {
                return new JsonResponse(['error' => $error], 400);
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
                    return new JsonResponse(['error' => 'Could not generate unique username.'], 500);
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

        return new JsonResponse(['status' => 'User created.'], 201);
    }
}
