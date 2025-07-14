<?php

namespace App\Service;

use App\Entity\FriendRequest;
use App\Entity\Invitation;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NotificationMercureService
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchFriendRequest(FriendRequest $friendRequest): void
    {
        dd("test");
        $this->eventDispatcher->dispatch($friendRequest, 'friend_request.created');
    }

    public function dispatchInvitation(Invitation $invitation): void
    {
        $this->eventDispatcher->dispatch($invitation, 'invitation.created');
    }
}
