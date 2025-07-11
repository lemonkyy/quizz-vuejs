<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use App\Repository\InvitationRepository;

//send an invitation to another user to join the current user's room
class MeSendController extends AbstractController
{
    private int $maxRoomUsers;
    public function __construct(ParameterBagInterface $params)
    {
        $this->maxRoomUsers = $params->get('app.max_room_users');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, Request $request, UserRepository $userRepository, RoomRepository $roomRepository, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$id) {
            return $this->json(['code' => 'ERR_MISSING_USER_ID', 'error' => 'Missing user_id'], 400);
        }

        if ($id == $user->getId()) {
            return $this->json(['code' => 'ERR_CANNOT_INVITE_SELF', 'error' => 'Cannot invite yourself'], 400);
        }

        $targetUser = $userRepository->find($id);
        if (!$targetUser) {
            return $this->json(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        $room = $roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            return $this->json(['code' => 'ERR_NOT_IN_A_ROOM', 'error' => 'You are not in a room'], 400);
        }

        if ($room->getRoomPlayers()->contains($targetUser->getRoomPlayer())) {
            return $this->json(['code' => 'ERR_USER_ALREADY_IN_ROOM', 'error' => 'User is already in the room'], 400);
        }

        if (count($room->getRoomPlayers()) >= $this->maxRoomUsers) {
            return $this->json(['code' => 'ERR_ROOM_FULL', 'error' => 'Room is at max capacity'], 400);
        }

        $existing = $invitationRepository->findOneBy([
            'room' => $room,
            'invitedBy' => $user,
            'invitedUser' => $targetUser,
            'acceptedAt' => null,
            'revokedAt' => null,
            'deniedAt' => null
        ]);

        if ($existing) {
            return $this->json(['code' => 'ERR_INVITATION_ALREADY_SENT', 'error' => 'Invitation already sent'], 400);
        }

        $invitation = new Invitation();
        $invitation->setRoom($room);
        $invitation->setInvitedBy($user);
        $invitation->setInvitedUser($targetUser);

        $entityManager->persist($invitation);
        $entityManager->flush();

        return $this->json(['code' => 'SUCCESS', 'message' => 'Invitation sent'], 201);
    }
}
