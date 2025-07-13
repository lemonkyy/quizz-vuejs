<?php

namespace App\Api\Dto\Invitation;

use Symfony\Component\Validator\Constraints as Assert;

class MeDenyDto
{
    #[Assert\NotBlank]
    public ?string $id = null;
}
