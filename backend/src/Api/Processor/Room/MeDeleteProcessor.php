<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Service\RoomMembershipService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Exception\ValidationException;

class MeDeleteProcessor implements ProcessorInterface
{
    public function __construct(private RoomRepository $roomRepository, private RoomMembershipService $roomMembershipService, private Security $security)
    {
    }

    /**
     * @param Room $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        $room = $this->roomRepository->findOneBy(['owner' => $user, 'deletedAt' => null]);

        if (!$room) {
            throw new ValidationException('ERR_ROOM_NOT_FOUND', 'No active room found for user as owner', 404);
        }

        $this->roomMembershipService->handleUserDeletingRoom($room);

        return;
    }
}
