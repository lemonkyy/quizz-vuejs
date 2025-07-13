<?php

namespace App\Api\Dto\FriendRequest;

use Symfony\Component\Validator\Constraints as Assert;

class MeDenyDto
{
    #[Assert\NotBlank]
    public ?string $id = null;
}
