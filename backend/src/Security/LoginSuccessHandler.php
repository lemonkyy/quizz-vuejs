<?php

namespace App\Security;

use App\Entity\User;
use App\Service\JWTCookieService;
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
    private JWTCookieService $cookieService;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        CacheInterface $cacheManager,
        JWTCookieService $cookieService)
    {
        $this->jwtManager = $jwtManager;
        $this->cacheManager = $cacheManager;
        $this->cookieService = $cookieService;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Invalid user'], 401);
        }

        if ($user->getTotpSecret()) {
            //user has TOTP enabled: generate a temporary token for TOTP verification
            $tempToken = $this->generateTempToken($user);

            return new JsonResponse([
                'code' => 'TOTP_REQUIRED',
                'message' => 'TOTP code required',
                'tempToken' => $tempToken
            ]);
        }

        $jwtToken = $this->jwtManager->create($user);

        $response = new JsonResponse([
            'code' => 'SUCCESS',
            'message' => 'Login successful'
        ]);

        [$cookieHp, $cookieS] = $this->cookieService->createCookies($jwtToken);
        $response->headers->setCookie($cookieHp);
        $response->headers->setCookie($cookieS);

        return $response;
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
}
