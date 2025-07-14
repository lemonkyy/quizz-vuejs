<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\UserRepository;
use App\Service\JWTCookieService;
use App\Service\TotpService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Api\Dto\User\VerifyTotpCodeDto;
use App\Exception\ValidationException;

class VerifyTotpCodeProcessor implements ProcessorInterface
{
    public function __construct(private TotpService $totpService, private JWTTokenManagerInterface $jwtManager, private CacheInterface $cache, private UserRepository $userRepository, private JWTCookieService $cookieService, private RequestStack $requestStack)
    {
    }

    /**
     * @param VerifyTotpCodeDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $tempToken = $data->tempToken;
        $totpCode = $data->totpCode;

        if (!$tempToken || !$totpCode) {
            throw new ValidationException('ERR_MISSING_TOTP_CREDENTIALS', 'Temporary token and TOTP code are required.', 400);
        }

        $cacheKey = 'temp_token_' . $tempToken;

        try {
            $userId = $this->cache->get($cacheKey, function (ItemInterface $item) {
                throw new \Exception('Cache miss');
            });
        } catch (\Exception $e) {
            throw new ValidationException('ERR_INVALID_TEMP_TOKEN', 'Invalid temporary token.', 401);
        }

        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not found.', 404);
        }

        $isValid = $this->totpService->verifyTotp($user->getTotpSecret(), $totpCode);
        if (!$isValid) {
            throw new ValidationException('ERR_INVALID_TOTP_CODE', 'Invalid TOTP code.', 401);
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
}
