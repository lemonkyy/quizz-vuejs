<?php

namespace App\Controller\Api\Room;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\RoomRepository;
use App\Service\RoomMembershipService;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//soft delete the room the current user is creator of
class MeDeleteController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, RoomRepository $roomRepository, RoomMembershipService $roomMembershipService): Response
    {

        $room = $roomRepository->findOneBy(['owner' => $user, 'deletedAt' => null]);

        if (!$room) {
            return $this->json(['code' => 'ERR_ROOM_NOT_FOUND', 'error' => 'No active room found for user as owner'], 404);
        }

        $roomMembershipService->handleUserDeletingRoom($room);

        return $this->json(['code' => 'SUCCESS', 'message' => 'Room deleted'], 200);
    }
}
