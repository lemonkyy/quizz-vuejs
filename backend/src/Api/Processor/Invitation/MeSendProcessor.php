<?php

namespace App\Api\Processor\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use App\Repository\InvitationRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Invitation;
use App\Entity\User;
use App\Api\Dto\Invitation\SendDto;

class MeSendProcessor implements ProcessorInterface
{
    private int $maxRoomUsers;
    private int $maxSentInvitations;

    public function __construct(ParameterBagInterface $params, private UserRepository $userRepository, private RoomRepository $roomRepository, private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager)
    {
        $this->maxRoomUsers = $params->get('app.max_room_users');
        $this->maxSentInvitations = $params->get('app.max_sent_invitations');
    }

    /**
     * @param SendDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $targetUserId = $data->invitedUserId;

        if (!$targetUserId) {
            return new JsonResponse(['code' => 'ERR_MISSING_USER_ID', 'error' => 'Missing user_id'], 400);
        }

        if ($targetUserId == $user->getId()) {
            return new JsonResponse(['code' => 'ERR_CANNOT_INVITE_SELF', 'error' => 'Cannot invite yourself'], 400);
        }

        $targetUser = $this->userRepository->find($targetUserId);
        if (!$targetUser) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        $room = $this->roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            return new JsonResponse(['code' => 'ERR_NOT_IN_A_ROOM', 'error' => 'You are not in a room'], 400);
        }

        if ($room->getRoomPlayers()->contains($targetUser->getRoomPlayer())) {
            return new JsonResponse(['code' => 'ERR_USER_ALREADY_IN_ROOM', 'error' => 'User is already in the room'], 400);
        }

        if (count($room->getRoomPlayers()) >= $this->maxRoomUsers) {
            return new JsonResponse(['code' => 'ERR_ROOM_FULL', 'error' => 'Room is at max capacity'], 400);
        }

        $existing = $this->invitationRepository->findOneBy([
            'room' => $room,
            'invitedBy' => $user,
            'invitedUser' => $targetUser,
            'acceptedAt' => null,
            'revokedAt' => null,
            'deniedAt' => null
        ]);

        if ($existing) {
            return new JsonResponse(['code' => 'ERR_INVITATION_ALREADY_SENT', 'error' => 'Invitation already sent'], 400);
        }

        if (count($user->getSentInvitations()) >= $this->maxSentInvitations) {
            return new JsonResponse(['code' => 'ERR_MAX_SENT_INVITATIONS_REACHED', 'error' => 'You have reached the maximum number of sent invitations'], 400);
        }

        $invitation = new Invitation();
        $invitation->setRoom($room);
        $invitation->setInvitedBy($user);
        $invitation->setInvitedUser($targetUser);

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Invitation sent'], 201);
    }
}
