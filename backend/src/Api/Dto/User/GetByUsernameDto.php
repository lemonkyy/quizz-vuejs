<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class GetByUsernameDto
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public ?string $username = null;
}
