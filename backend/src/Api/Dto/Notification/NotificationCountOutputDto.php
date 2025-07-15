<?php

namespace App\Api\Dto\Notification;

use Symfony\Component\Serializer\Annotation\Groups;

class NotificationCountOutputDto
{
    #[Groups(['notification:read'])]
    public int $notificationCount;

    public function __construct(int $notificationCount)
    {
        $this->notificationCount = $notificationCount;
    }
}
