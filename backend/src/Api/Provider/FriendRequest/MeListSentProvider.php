<?php

namespace App\Api\Provider\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Entity\User;
use App\Exception\ValidationException;

class MeListSentProvider implements ProviderInterface
{
    public function __construct(private FriendRequestRepository $friendRequestRepository, private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === FriendRequest::class && $operationName === 'api_friend_requests_sent';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        $criteria = [
            'sender' => $user,
            'acceptedAt' => null,
            'deniedAt' => null,
            'revokedAt' => null
        ];

        return $this->friendRequestRepository->findBy($criteria);
    }
}
