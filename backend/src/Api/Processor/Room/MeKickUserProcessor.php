<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\RoomMembershipService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;
use App\Entity\User;
use App\Api\Dto\Room\KickUserDto;

class MeKickUserProcessor implements ProcessorInterface
{
    public function __construct(private RoomRepository $roomRepository, private UserRepository $userRepository, private RoomMembershipService $roomMembershipService)
    {
    }

    /**
     * @param KickUserDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $targetUserId = $data->targetUserId;

        if (!$targetUserId) {
            return new JsonResponse(['code' => 'ERR_MISSING_USER_ID', 'error' => 'Missing user_id'], 400);
        }

        if ($targetUserId == $user->getId()) {
            return new JsonResponse(['code' => 'ERR_CANNOT_KICK_SELF', 'error' => 'You cannot kick yourself'], 400);
        }

        $room = $this->roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            return new JsonResponse(['code' => 'ERR_NOT_IN_A_ROOM', 'error' => 'You are not in a room'], 400);
        }

        if ($room->getOwner()->getId() !== $user->getId()) {
            return new JsonResponse(['code' => 'ERR_NOT_ROOM_OWNER', 'error' => 'Only the room owner can kick users'], 403);
        }

        $targetUser = $this->userRepository->find($targetUserId);
        if (!$targetUser) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        if (!$room->getRoomPlayers()->contains($targetUser->getRoomPlayer())) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_IN_ROOM', 'error' => 'User is not in your room'], 400);
        }

        $this->roomMembershipService->handleUserKickedFromRoom($user, $room);

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'User kicked from room'], 200);
    }
}
