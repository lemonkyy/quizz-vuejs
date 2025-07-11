<?php

namespace App\Controller\Api\Room;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\RoomMembershipService;

//kick a user from the current room (only by room creator)
class MeKickUserController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, Request $request, RoomRepository $roomRepository, UserRepository $userRepository, RoomMembershipService $roomMembershipService): Response
    {
        if (!$id) {
            return $this->json(['code' => 'ERR_MISSING_USER_ID', 'error' => 'Missing user_id'], 400);
        }

        if ($id == $user->getId()) {
            return $this->json(['code' => 'ERR_CANNOT_KICK_SELF', 'error' => 'You cannot kick yourself'], 400);
        }

        $room = $roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            return $this->json(['code' => 'ERR_NOT_IN_A_ROOM', 'error' => 'You are not in a room'], 400);
        }

        if ($room->getOwner()->getId() !== $user->getId()) {
            return $this->json(['code' => 'ERR_NOT_ROOM_OWNER', 'error' => 'Only the room owner can kick users'], 403);
        }

        $targetUser = $userRepository->find($id);
        if (!$targetUser) {
            return $this->json(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        if (!$room->getRoomPlayers()->contains($targetUser->getRoomPlayer())) {
            return $this->json(['code' => 'ERR_USER_NOT_IN_ROOM', 'error' => 'User is not in your room'], 400);
        }

        $roomMembershipService->handleUserKickedFromRoom($user, $room);

        return $this->json(['code' => 'SUCCESS', 'message' => 'User kicked from room'], 200);
    }
}
