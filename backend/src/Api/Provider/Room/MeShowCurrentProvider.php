<?php

namespace App\Api\Provider\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\RoomRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;
use App\Entity\User;
use App\Exception\ValidationException;

class MeShowCurrentProvider implements ProviderInterface
{
    public function __construct(private RoomRepository $roomRepository, private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Room::class && $operationName === 'api_user_room';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        return $this->roomRepository->findActiveRoomForUser($user);
    }
}