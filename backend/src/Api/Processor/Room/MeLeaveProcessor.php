<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Service\RoomMembershipService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Exception\ValidationException;

class MeLeaveProcessor implements ProcessorInterface
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
        $room = $this->roomRepository->findActiveRoomForUser($user);

        if (!$room) {
            throw new ValidationException('ERR_NOT_IN_A_ROOM', 'User is not in an active room', 400);
        }

        $this->roomMembershipService->handleUserLeavingRoom($user);

        return;
    }
}
