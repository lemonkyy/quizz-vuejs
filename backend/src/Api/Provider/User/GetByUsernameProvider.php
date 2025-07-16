<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use App\Api\Dto\User\GetByUsernameDto;
use App\Entity\User;

class GetByUsernameProvider implements ProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []):
    bool
    {
        return $resourceClass === User::class && $operationName === 'api_get_user_by_username';
    }

    /**
     * @param GetByUsernameDto $data
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        $username = $context['request']->query->get('username');

        if (!$username) {
            return null;
        }

        return $this->userRepository->findOneBy(['username' => $username]);
    }
}
