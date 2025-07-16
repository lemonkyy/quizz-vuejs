<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\RoomMembershipService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;
use App\Entity\User;
use App\Api\Dto\Room\KickUserDto;
use App\Exception\ValidationException;

class MeKickUserProcessor implements ProcessorInterface
{
    public function __construct(private RoomRepository $roomRepository, private UserRepository $userRepository, private RoomMembershipService $roomMembershipService, private Security $security)
    {
    }

    /**
     * @param KickUserDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Room
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        $targetUserId = $uriVariables['id'];

        if (!$targetUserId) {
            throw new ValidationException('ERR_MISSING_USER_ID', 'Missing user_id', 400);
        }

        if ($targetUserId == $user->getId()) {
            throw new ValidationException('ERR_CANNOT_KICK_SELF', 'You cannot kick yourself', 400);
        }

        $room = $this->roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            throw new ValidationException('ERR_NOT_IN_A_ROOM', 'You are not in a room', 400);
        }

        if ($room->getOwner()->getId() !== $user->getId()) {
            throw new ValidationException('ERR_NOT_ROOM_OWNER', 'Only the room owner can kick users', 403);
        }

        $targetUser = $this->userRepository->find($targetUserId);
        if (!$targetUser) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not found', 404);
        }

        if (!$room->getRoomPlayers()->contains($targetUser->getRoomPlayer())) {
            throw new ValidationException('ERR_USER_NOT_IN_ROOM', 'User is not in your room', 400);
        }

        $this->roomMembershipService->handleUserKickedFromRoom($user, $room);

        return $room;
    }
}
