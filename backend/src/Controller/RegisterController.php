<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Invalid email address'], 400);
        }

        $user = new User();

        if ($entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already in use'], 400);
        }

        $maxAttempts = 25;
        $attempts = 0;
        do {
            $randomNumber = random_int(100000000, 999999999);
            $randomUsername = 'User#' . $randomNumber;
            $attempts++;
            if ($attempts > $maxAttempts) {
                return new JsonResponse(['error' => 'Could not generate unique username.'], 500);
            }
        } while ($entityManager->getRepository(User::class)->findOneBy(['username' => $randomUsername]));

        $user->setUsername($randomUsername);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User created.'], 201);
    }
}
