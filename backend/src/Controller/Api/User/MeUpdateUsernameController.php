<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeUpdateUsernameController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !is_string($data['username']) || strlen($data['username']) <= 1 || strlen($data['username']) > 20) {
            return new JsonResponse(['error' => 'Invalid username'], 400);
        }

        if ($userRepository->findOneBy(['username' => $data['username']])) {
            return new JsonResponse(['error' => 'Username already in use'], 400);
        }
        $user->setUsername($data['username']);
        $entityManager->flush();
        
        return new JsonResponse(['status' => 'Username updated', 'username' => $user->getUsername()]);
    }
}
