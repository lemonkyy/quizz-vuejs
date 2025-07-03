<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    private JWTTokenManagerInterface $jwtManager;
    private CacheInterface $cacheManager;

    public function __construct(JWTTokenManagerInterface $jwtManager, CacheInterface $cacheManager)
    {
        $this->jwtManager = $jwtManager;
        $this->cacheManager = $cacheManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Invalid user'], 401);
        }

        if ($user->getTOTPSecret()) {
            //user has TOTP enabled: generate a temporary token for TOTP verification
            $tempToken = $this->generateTempToken($user);

            return new JsonResponse([
                'message' => 'TOTP code required',
                'temp_token' => $tempToken
            ]);
        }

        $jwtToken = $this->generateJwtToken($user);
        return new JsonResponse(['token' => $jwtToken]);
    }

    private function generateTempToken($user): string
    {
        $tempToken = bin2hex(random_bytes(32));

        $this->storeTempToken($user, $tempToken);

        return $tempToken;
    }

    private function storeTempToken($user, string $tempToken): void
    {
        $cacheKey = 'temp_token_' . $tempToken;

        $this->cacheManager->get($cacheKey, function (ItemInterface $item) use ($user) {
            $item->expiresAfter(120); //cache for 2 mins
            return $user->getId();
        });
    }

    private function generateJwtToken($user): string
    {
        return $this->jwtManager->create($user);
    }
}
