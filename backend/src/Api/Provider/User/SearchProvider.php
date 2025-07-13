<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\User;
use App\Api\Dto\User\SearchDto;

class SearchProvider implements ProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class && $operationName === 'api_user_search';
    }

    /**
     * @param SearchDto $data
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $username = $context['request']->query->get('username');
        $page = $context['request']->query->getInt('page', 1);
        $limit = $context['request']->query->getInt('limit', 10);

        return $this->userRepository->findByUsernamePaginated($username, $page, $limit);
    }
}
