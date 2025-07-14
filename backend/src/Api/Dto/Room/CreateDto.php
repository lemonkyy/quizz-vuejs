<?php

namespace App\Api\Dto\Room;

use Symfony\Component\Validator\Constraints as Assert;

class CreateDto
{
    #[Assert\NotNull]
    #[Assert\Type('bool')]
    public ?bool $isPublic = null;
}
