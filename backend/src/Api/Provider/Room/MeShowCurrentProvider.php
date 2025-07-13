<?php

namespace App\Api\Provider\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\RoomRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;

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

        return $this->roomRepository->findActiveRoomForUser($user);
    }
}