<?php

namespace App\Controller\Api\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MeListFriendsController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, Request $request, UserRepository $userRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $username = $request->query->get('username');

        $friendsData = $userRepository->findFriendsByUserIdPaginated(
            $user->getId()->__toString(),
            $page,
            $limit,
            $username
        );

        return $this->json([
            'code' => 'SUCCESS',
            'friends' => $friendsData['friends'],
            'hasMore' => $friendsData['hasMore'],
        ], 200, [], ['groups' => ['user:read']]);
    }
}
