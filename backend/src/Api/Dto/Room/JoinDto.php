<?php

namespace App\Api\Dto\Room;

use Symfony\Component\Validator\Constraints as Assert;

class JoinDto
{
    #[Assert\NotBlank]
    public ?string $roomId = null;
}
