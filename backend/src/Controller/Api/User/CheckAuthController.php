<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CheckAuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_check_auth', methods: ['GET'])]
    public function checkAuthAction(): JsonResponse
    {
        if(is_null($this->getUser())) {
            return new JsonResponse(['code' => 'ERR_UNAUTHORIZED', 'error' => 'Unauthorized'], 401);
        }else {
            return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Authorized'], 200);
        }
    }

    #[Route('/api/user/logout', name: 'api_logout')]
    public function logout(): JsonResponse
    {
        throw new \LogicException('Cette méthode est interceptée par la configuration de sécurité.');
    }
}