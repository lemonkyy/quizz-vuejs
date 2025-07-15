<?php

namespace App\Service;

use App\Entity\FriendRequest;
use App\Entity\Invitation;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationMercureService
{
    private HubInterface $publisher;

    public function __construct(HubInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    public function publishNotificationUpdate(string $userId): void
    {
        $update = new Update(
            sprintf('/notifications/%s', $userId),
            json_encode(['status' => 'new_notification'])
        );

        ($this->publisher)($update);
    }

    public function notifyFriendRequestUpdate(FriendRequest $friendRequest): void
    {
        $this->publishNotificationUpdate($friendRequest->getReceiver()->getId());
    }

    public function notifyInvitationUpdate(Invitation $invitation): void
    {
        $this->publishNotificationUpdate($invitation->getReceiver()->getId());
    }
}
