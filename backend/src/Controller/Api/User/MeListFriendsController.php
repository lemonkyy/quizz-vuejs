<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MeListFriendsController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user): Response
    {
        $friends = $user->getFriends();

        return $this->json(['code' => 'SUCCESS', 'friends' => $friends], 200, [], ['groups' => ['user:read']]);
    }
}
