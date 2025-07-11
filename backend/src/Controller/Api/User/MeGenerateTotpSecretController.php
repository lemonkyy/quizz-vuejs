<?php

namespace App\Controller\Api\User;

use App\Service\JWTCookieService;
use App\Service\TotpService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//generate TOTP secret
class MeGenerateTotpSecretController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, TotpService $totpService, EntityManagerInterface $entityManager, JWTCookieService $cookieService, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $totpSecret = $totpService->generateSecretKey();
        $user->setTotpSecret($totpSecret);

        $entityManager->persist($user);
        $entityManager->flush();

        $jwtToken = $jwtManager->create($user);

        $response = new JsonResponse(['code' => 'SUCCESS', 'totpSecret' => $totpSecret], 200);

        [$cookieHp, $cookieS] = $cookieService->createCookies($jwtToken);
        $response->headers->setCookie($cookieHp);
        $response->headers->setCookie($cookieS);

        return $response;
    }
}
