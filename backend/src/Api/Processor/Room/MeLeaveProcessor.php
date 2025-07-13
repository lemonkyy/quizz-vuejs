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

class MeLeaveProcessor implements ProcessorInterface
{
    public function __construct(private RoomRepository $roomRepository, private RoomMembershipService $roomMembershipService)
    {
    }

    /**
     * @param Room $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $room = $this->roomRepository->findActiveRoomForUser($user);

        if (!$room) {
            return new JsonResponse(['code' => 'ERR_NOT_IN_A_ROOM', 'error' => 'User is not in an active room'], 400);
        }

        $this->roomMembershipService->handleUserLeavingRoom($user);

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Left room successfully'], 200);
    }
}
