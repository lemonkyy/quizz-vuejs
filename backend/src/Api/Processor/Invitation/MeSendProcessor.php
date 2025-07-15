<?php

namespace App\Api\Processor\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use App\Repository\InvitationRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Invitation;
use App\Api\Dto\Invitation\SendDto;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Service\NotificationMercureService;

class MeSendProcessor implements ProcessorInterface
{
    private int $maxRoomUsers;
    private int $maxSentInvitations;
    private int $maxReceivedInvitations;

    public function __construct(ParameterBagInterface $params, private UserRepository $userRepository, private RoomRepository $roomRepository, private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager, private Security $security, private NotificationMercureService $notificationMercureService)
    {
        $this->maxRoomUsers = $params->get('app.max_room_users');
        $this->maxSentInvitations = $params->get('app.max_sent_invitations');
        $this->maxReceivedInvitations = $params->get('app.max_received_invitations');
    }

    /**
     * @param SendDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Invitation
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        $targetUserId = $uriVariables['id'];

        if (!$targetUserId) {
            throw new ValidationException('ERR_MISSING_USER_ID', 'Missing user_id', 400);
        }

        if ($targetUserId == $user->getId()) {
            throw new ValidationException('ERR_CANNOT_INVITE_SELF', 'Cannot invite yourself', 400);
        }

        $targetUser = $this->userRepository->find($targetUserId);
        if (!$targetUser) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not found', 404);
        }

        $room = $this->roomRepository->findActiveRoomForUser($user);
        if (!$room) {
            throw new ValidationException('ERR_NOT_IN_A_ROOM', 'You are not in a room', 400);
        }

        if (count($room->getRoomPlayers()) >= $this->maxRoomUsers) {
            throw new ValidationException('ERR_ROOM_FULL', 'Room is at max capacity', 400);
        }

        $existing = $this->invitationRepository->findOneBy([
            'room' => $room,
            'sender' => $user,
            'receiver' => $targetUser,
            'acceptedAt' => null,
            'revokedAt' => null,
            'deniedAt' => null
        ]);

        if ($existing) {
            throw new ValidationException('ERR_INVITATION_ALREADY_SENT', 'Invitation already sent', 400);
        }

        if (count($user->getSentInvitations()) >= $this->maxSentInvitations) {
            throw new ValidationException('ERR_MAX_SENT_INVITATIONS_REACHED', 'You have reached the maximum number of sent invitations', 400);
        }

        if (count($user->getReceivedInvitations()) >= $this->maxReceivedInvitations) {
            throw new ValidationException('ERR_MAX_RECEIVED_INVITATIONS_REACHED', 'You have reached the maximum number of received invitations', 400);
        }

        $invitation = new Invitation();
        $invitation->setRoom($room);
        $invitation->setSender($user);
        $invitation->setReceiver($targetUser);

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        $this->notificationMercureService->notifyInvitationUpdate($invitation);

        return $invitation;
    }
}
