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

//kick a user from the current room (only by room creator)
class MeKickUserController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, Request $request, RoomRepository $roomRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $targetUserId = $data['user_id'] ?? null;

        if (!$targetUserId) {
            return $this->json(['code' => 'ERR_MISSING_USER_ID', 'error' => 'Missing user_id'], 400);
        }

        if ($targetUserId == $user->getId()) {
            return $this->json(['code' => 'ERR_CANNOT_KICK_SELF', 'error' => 'You cannot kick yourself'], 400);
        }

        $room = $roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            return $this->json(['code' => 'ERR_NOT_IN_A_ROOM', 'error' => 'You are not in a room'], 400);
        }

        if ($room->getOwner()->getId() !== $user->getId()) {
            return $this->json(['code' => 'ERR_NOT_ROOM_OWNER', 'error' => 'Only the room owner can kick users'], 403);
        }

        $targetUser = $userRepository->find($targetUserId);
        if (!$targetUser) {
            return $this->json(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        if (!$room->getUsers()->contains($targetUser)) {
            return $this->json(['code' => 'ERR_USER_NOT_IN_ROOM', 'error' => 'User is not in your room'], 400);
        }

        $room->removeUser($targetUser);

        $entityManager->flush();

        return $this->json(['code' => 'SUCCESS', 'message' => 'User kicked from room'], 200);
    }
}
