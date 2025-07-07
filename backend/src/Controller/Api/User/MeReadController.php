<?php

namespace App\Controller\Api\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MeReadController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user): JsonResponse
    {
        return $this->json(['code' => 'SUCCESS', 'user' => $user], 200, [], ['groups' => ['user:read']]);
    }
}
