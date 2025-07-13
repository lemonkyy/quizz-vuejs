<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class MeReadProvider implements ProviderInterface
{
    public function __construct(private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class && $operationName === 'api_user_info';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?UserInterface
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('User is not authenticated');
        }
        return $user;
    }
}
