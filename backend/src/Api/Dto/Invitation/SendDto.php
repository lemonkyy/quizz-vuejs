<?php

namespace App\Api\Dto\Invitation;

use Symfony\Component\Validator\Constraints as Assert;

class SendDto
{
    #[Assert\NotBlank]
    public ?string $invitedUserId = null;
}
