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

class MeDeleteProcessor implements ProcessorInterface
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
        $room = $this->roomRepository->findOneBy(['owner' => $user, 'deletedAt' => null]);

        if (!$room) {
            return new JsonResponse(['code' => 'ERR_ROOM_NOT_FOUND', 'error' => 'No active room found for user as owner'], 404);
        }

        $this->roomMembershipService->handleUserDeletingRoom($room);

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Room deleted'], 200);
    }
}
