<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\TOTPService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

//handles TOTP code check
class VerifyTOTPCode extends AbstractController
{
    public function __invoke(Request $request, TOTPService $TOTPService, JWTTokenManagerInterface $jwtManager, CacheInterface $cache, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tempToken = $data['temp_token'] ?? null;
        $totpCode = $data['totp_code'] ?? null;

        if (!$tempToken || !$totpCode) {
            return new JsonResponse(['error' => 'Temporary token and TOTP code are required.'], 400);
        }

        $cacheKey = 'temp_token_' . $tempToken;

        try {
            $userId = $cache->get($cacheKey, function (ItemInterface $item) {
                throw new \Exception('Cache miss');
            });
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid temporary token.'], 401);
        }

        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], 404);
        }

        if (!$TOTPService->verifyTOTP($user->getTOTPSecret(), $totpCode)) {
            return new JsonResponse(['error' => 'Invalid TOTP code.'], 401);
        }

        $jwtToken = $jwtManager->create($user);

        return new JsonResponse(['token' => $jwtToken]);
    }
}
