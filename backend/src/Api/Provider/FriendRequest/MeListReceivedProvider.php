<?php

namespace App\Api\Provider\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;

class MeListReceivedProvider implements ProviderInterface
{
    public function __construct(private FriendRequestRepository $friendRequestRepository, private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === FriendRequest::class && $operationName === 'api_friend_requests_received';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        $criteria = [
            'receiver' => $user,
            'acceptedAt' => null,
            'deniedAt' => null,
            'revokedAt' => null
        ];

        return $this->friendRequestRepository->findBy($criteria);
    }
}
