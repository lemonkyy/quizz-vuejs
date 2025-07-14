<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\JWTCookieService;
use App\Service\TotpService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Exception\ValidationException;

class MeGenerateTotpSecretProcessor implements ProcessorInterface
{
    public function __construct(private TotpService $totpService, private EntityManagerInterface $entityManager, private JWTCookieService $cookieService, private JWTTokenManagerInterface $jwtManager, private Security $security)
    {
    }

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        $totpSecret = $this->totpService->generateSecretKey();
        $user->setTotpSecret($totpSecret);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $jwtToken = $this->jwtManager->create($user);

        $response = new JsonResponse(['code' => 'SUCCESS', 'totpSecret' => $totpSecret], 200);

        [$cookieHp, $cookieS] = $this->cookieService->createCookies($jwtToken);
        $response->headers->setCookie($cookieHp);
        $response->headers->setCookie($cookieS);

        return $response;
    }
}
