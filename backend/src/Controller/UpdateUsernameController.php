<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UpdateUsernameController extends AbstractController
{
    #[Route('/api/user/username', name: 'api_update_username', methods: ['PUT'])]
    public function __invoke(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !is_string($data['username']) || strlen($data['username']) <= 1 || strlen($data['username']) > 20) {
            return new JsonResponse(['error' => 'Invalid username'], 400);
        }

        if ($entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']])) {
            return new JsonResponse(['error' => 'Username already in use'], 400);
        }
        $user->setUsername($data['username']);
        $entityManager->flush();
        return new JsonResponse(['status' => 'Username updated', 'username' => $user->getUsername()]);
    }
}
