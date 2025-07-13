<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;

class CheckAuthProvider implements ProviderInterface
{
    public function __construct(private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class && $operationName === 'api_check_auth';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        $user = $this->security->getUser();

        if (is_null($user)) {
            // Returning null from a provider for a GET operation typically results in a 404.
            // For authentication checks, a custom error handler or a dedicated controller might be more appropriate
            // if a 401 response is strictly required without a 404 first.
            // However, to fit the provider pattern, we return null if not authenticated.
            return null;
        }

        return $user;
    }
}
