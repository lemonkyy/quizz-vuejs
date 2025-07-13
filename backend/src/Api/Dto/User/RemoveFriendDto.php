<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class RemoveFriendDto
{
    #[Assert\NotBlank]
    public ?string $friendId = null;
}
