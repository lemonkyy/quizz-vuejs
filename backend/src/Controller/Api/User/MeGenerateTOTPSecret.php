<?php

namespace App\Controller\Api\User;

use App\Service\TOTPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//generate TOTP secret
class MeGenerateTOTPSecret extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, TOTPService $TOTPService, EntityManagerInterface $entityManager): JsonResponse
    {
        $TOTPkey = $TOTPService->generateSecretKey();

        if (!$TOTPkey) {
            return new JsonResponse(['error' => 'Failed to generate TOTP key.'], 500);
        }

        $user->setTOTPSecret($TOTPkey);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['TOTP Secret' => $TOTPkey], 200);
    }
}
