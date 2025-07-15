<?php

namespace App\Enum;

enum NotificationType: string
{
    case INVITATION = 'invitation';
    case FRIEND_REQUEST = 'friend_request';
}
