<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateDto
{
    #[Assert\Type('string')]
    public ?string $newUsername = null;

    #[Assert\Type('string')]
    public ?string $newProfilePictureId = null;

    #[Assert\Type('bool')]
    public ?bool $clearTotpSecret = null;
}
