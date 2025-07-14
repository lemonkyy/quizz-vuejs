<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\FriendRequestRepository;
use App\Repository\InvitationRepository;
use Symfony\Bundle\SecurityBundle\Security;

class MeListNotificationsProvider implements ProviderInterface
{
    public function __construct(private Security $security, private FriendRequestRepository $friendRequestRepository, private InvitationRepository $invitationRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        $friendRequests = $this->friendRequestRepository->findActiveFriendRequestsForUser($user);
        $invitations = $this->invitationRepository->findActiveForUser($user);

        $notifications = [];

        foreach ($friendRequests as $friendRequest) {
            $notifications[] = [
                'type' => 'friend_request',
                'id' => $friendRequest->getId(),
                'createdAt' => $friendRequest->getSentAt(),
                'sender' => [
                    'id' => $friendRequest->getSender()->getId(),
                    'username' => $friendRequest->getSender()->getUsername(),
                ],
            ];
        }

        foreach ($invitations as $invitation) {
            $notifications[] = [
                'type' => 'invitation',
                'id' => $invitation->getId(),
                'createdAt' => $invitation->getInvitedAt(),
                'sender' => [
                    'id' => $invitation->getInvitedBy()->getId(),
                    'username' => $invitation->getInvitedBy()->getUsername(),
                ],
                'room' => [
                    'id' => $invitation->getRoom()->getId(),
                    'name' => $invitation->getRoom()->getName(),
                ],
            ];
        }

        usort($notifications, function ($a, $b) {
            return $b['createdAt'] <=> $a['createdAt'];
        });

        return $notifications;
    }
}
