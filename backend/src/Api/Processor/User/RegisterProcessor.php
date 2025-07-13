<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\ProfilePictureRepository;
use App\Repository\UserRepository;
use App\Service\ValidateUsernameService;
use App\Service\ValidatePasswordService;
use App\Entity\User;
use App\Api\Dto\User\RegisterDto;

class RegisterProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher, private UserRepository $userRepository, private ValidateUsernameService $validateUsernameService, private ValidatePasswordService $validatePasswordService, private ProfilePictureRepository $profilePictureRepository)
    {
    }

    /**
     * @param RegisterDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        if (!isset($data->email, $data->password)) {
            return new JsonResponse(['code' => 'ERR_MISSING_CREDENTIALS', 'error' => 'Missing email or password'], 400);
        }

        if (!$data->tosAgreedTo) {
            return new JsonResponse(['code' => 'ERR_TOS_REFUSED', 'error' => 'TOS not agreed to'], 400);
        }

        $passwordValidation = $this->validatePasswordService->validate($data->password);
        if ($passwordValidation) {
            return new JsonResponse($passwordValidation, 400);
        }

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['code' => 'ERR_INVALID_EMAIL', 'error' => 'Invalid email address'], 400);
        }

        if ($this->userRepository->findOneBy(['email' => $data->email])) {
            return new JsonResponse(['code' => 'ERR_EMAIL_ALREADY_IN_USE', 'error' => 'Email already in use'], 400);
        }

        if (isset($data->username) && is_string($data->username)) {

            $username = $data->username;
            $error = $this->validateUsernameService->validate($username);

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
                $error = $this->validateUsernameService->validate($username);
                if ($attempts > $maxAttempts) {
                    return new JsonResponse(['code' => 'ERR_USERNAME_GENERATION_FAILED', 'error' => 'Could not generate unique username.'], 500);
                }

            } while ($error);
        }

        $profilePictures = $this->profilePictureRepository->findAll();
        $randomProfilePicture = $profilePictures[array_rand($profilePictures)];

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($data->email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data->password));
        $user->setProfilePicture($randomProfilePicture);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'User created.'], 201);
    }
}
