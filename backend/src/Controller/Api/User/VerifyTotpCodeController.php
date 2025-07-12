<?php

namespace App\Controller\Api\User;

use App\Repository\UserRepository;
use App\Service\JWTCookieService;
use App\Service\TotpService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

//handles TOTP code check
class VerifyTotpCodeController extends AbstractController
{
    public function __invoke(Request $request, TotpService $totpService, JWTTokenManagerInterface $jwtManager, CacheInterface $cache, UserRepository $userRepository, JWTCookieService $cookieService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tempToken = $data['tempToken'] ?? null;
        $totpCode = $data['totpCode'] ?? null;

        if (!$tempToken || !$totpCode) {
            return new JsonResponse(['code' => 'ERR_MISSING_TOTP_CREDENTIALS', 'error' => 'Temporary token and TOTP code are required.'], 400);
        }

        $cacheKey = 'temp_token_' . $tempToken;

        try {
            $userId = $cache->get($cacheKey, function (ItemInterface $item) {
                throw new \Exception('Cache miss');
            });
        } catch (\Exception $e) {
            return new JsonResponse(['code' => 'ERR_INVALID_TEMP_TOKEN', 'error' => 'Invalid temporary token.'], 401);
        }

        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found.'], 404);
        }

        $isValid = $totpService->verifyTotp($user->getTotpSecret(), $totpCode);
        if (!$isValid) {
            return new JsonResponse(['code' => 'ERR_INVALID_TOTP_CODE', 'error' => 'Invalid TOTP code.'], 401);
        }

        $jwtToken = $jwtManager->create($user);

        $response = new JsonResponse([
            'code' => 'SUCCESS',
            'message' => 'Login successful'
        ]);

        [$cookieHp, $cookieS] = $cookieService->createCookies($jwtToken);
        $response->headers->setCookie($cookieHp);
        $response->headers->setCookie($cookieS);

        return $response;
    }
}
