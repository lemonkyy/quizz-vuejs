<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyTotpCodeDto
{
    #[Assert\NotBlank]
    public ?string $tempToken = null;

    #[Assert\NotBlank]
    public ?string $totpCode = null;
}
