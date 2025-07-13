<?php

namespace App\Api\Dto\Invitation;

use Symfony\Component\Validator\Constraints as Assert;

class MeCancelDto
{
    #[Assert\NotBlank]
    public ?string $id = null;
}
