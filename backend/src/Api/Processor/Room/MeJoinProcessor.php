<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\RoomRepository;
use App\Service\RoomMembershipService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;
use App\Entity\User;
use App\Api\Dto\Room\JoinDto;

class MeJoinProcessor implements ProcessorInterface
{
    public function __construct(private RoomRepository $roomRepository, private RoomMembershipService $roomMembershipService)
    {
    }

    /**
     * @param JoinDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $roomId = $data->roomId;

        $room = $this->roomRepository->findOneBy(['id' => $roomId]);

        if (!$room) {
            return new JsonResponse(['code' => 'ERR_ROOM_NOT_FOUND', 'error' => 'Room not found'], 404);
        }

        if ($room->getDeletedAt() !== null) {
            return new JsonResponse(['code' => 'ERR_ROOM_DELETED', 'error' => 'Room has been deleted'], 400);
        }

        if ($room->getRoomPlayers()->contains($user->getRoomPlayer())) {
            return new JsonResponse(['code' => 'ERR_USER_ALREADY_IN_ROOM', 'error' => 'User is already in this room'], 400);
        }

        $this->roomMembershipService->handleUserJoiningRoom($user, $room);

        return new JsonResponse(['code' => 'SUCCESS', 'room' => $room], 200, [], ['groups' => ['room:read']]);
    }
}
