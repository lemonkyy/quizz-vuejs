<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $password = null;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    #[Assert\IsTrue(message: 'You must agree to the terms of service.')]
    public ?bool $tosAgreedTo = null;

    #[Assert\Type('string')]
    public ?string $username = null;
}
