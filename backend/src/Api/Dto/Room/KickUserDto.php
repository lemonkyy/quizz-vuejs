<?php

namespace App\Api\Dto\Room;

use Symfony\Component\Validator\Constraints as Assert;

class KickUserDto
{
    #[Assert\NotBlank]
    public ?string $targetUserId = null;
}
