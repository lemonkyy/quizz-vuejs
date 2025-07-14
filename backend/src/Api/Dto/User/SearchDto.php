<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class SearchDto
{
    #[Assert\Type('string')]
    public ?string $username = null;

    #[Assert\Type('int')]
    public ?int $page = 1;

    #[Assert\Type('int')]
    public ?int $limit = 10;
}
