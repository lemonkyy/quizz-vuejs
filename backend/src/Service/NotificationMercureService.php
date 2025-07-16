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
    {@
        $this->publisher = $publisher;
    }

    public function publishNotificationUpdate(string $userId): void
    {
        error_log(sprintf('Attempting to publish Mercure update for user: %s', $userId));
        $update = new Update(
            sprintf('/notifications/%s', $userId),
            json_encode(['status' => 'new_notification'])
        );

        try {
            $this->publisher->publish($update);
            error_log(sprintf('Successfully published Mercure update for user: %s', $userId));
        } catch (\Exception $e) {
            error_log(sprintf('Failed to publish Mercure update for user %s: %s', $userId, $e->getMessage()));
        }
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
