<?php

namespace App\Api\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserSearchDto
{
    #[Assert\NotBlank(message: 'Username must not be blank')]
    #[Assert\Type('string')]
    public ?string $username = null;

    #[Assert\Positive(message: 'Page must be a positive integer')]
    public int $page = 1;

    #[Assert\Positive(message: 'Limit must be a positive integer')]
    #[Assert\LessThanOrEqual(value: 100, message: 'Limit cannot be more than 100')]
    public int $limit = 10;
}
