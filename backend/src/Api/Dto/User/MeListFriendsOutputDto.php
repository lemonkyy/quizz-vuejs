<?php

namespace App\Api\Dto\User;

use Symfony\Component\Serializer\Annotation\Groups;

class MeListFriendsOutputDto
{
    #[Groups(['user:read'])]
    public array $friends;

    #[Groups(['user:read'])]
    public bool $hasMore;

    public function __construct(array $friends, bool $hasMore)
    {
        $this->friends = $friends;
        $this->hasMore = $hasMore;
    }
}
