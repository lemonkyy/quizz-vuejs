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
    public function __invoke(#[CurrentUser] $user, Request $request, UserRepository $userRepository, RoomRepository $roomRepository, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $targetUserId = $data['user_id'] ?? null;

        if (!$targetUserId) {
            return $this->json(['error' => 'Missing user_id'], 400);
        }

        if ($targetUserId == $user->getId()) {
            return $this->json(['error' => 'Cannot invite yourself'], 400);
        }

        $targetUser = $userRepository->find($targetUserId);
        if (!$targetUser) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $room = $roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            return $this->json(['error' => 'You are not in a room'], 400);
        }

        if ($room->getUsers()->contains($targetUser)) {
            return $this->json(['error' => 'User is already in the room'], 400);
        }

        if (count($room->getUsers()) >= $this->maxRoomUsers) {
            return $this->json(['error' => 'Room is at max capacity'], 400);
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
            return $this->json(['error' => 'Invitation already sent'], 400);
        }

        $invitation = new Invitation();
        $invitation->setRoom($room);
        $invitation->setInvitedBy($user);
        $invitation->setInvitedUser($targetUser);

        $entityManager->persist($invitation);
        $entityManager->flush();

        return $this->json(['message' => 'Invitation sent'], 201);
    }
}
