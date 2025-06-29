<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserInfoController extends AbstractController
{
    #[Route('/api/user/info', name: 'api_user_info', methods: ['GET'])]
    public function __invoke(#[CurrentUser] $user): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return new JsonResponse([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ]);
    }
}
