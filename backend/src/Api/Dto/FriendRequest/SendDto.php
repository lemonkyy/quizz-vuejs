<?php

namespace App\Api\Dto\FriendRequest;

use Symfony\Component\Validator\Constraints as Assert;

class SendDto
{
    #[Assert\NotBlank]
    public ?string $receiverId = null;
}
