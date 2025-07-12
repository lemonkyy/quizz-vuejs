<?php

namespace App\Controller\Api\FriendRequest;

use App\Repository\FriendRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//list sent friend requests
class MeListSentController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, FriendRequestRepository $friendRequestRepository): Response
    {
        $criteria = [
            'sender' => $user,
            'acceptedAt' => null,
            'deniedAt' => null,
            'revokedAt' => null
        ];

        $friendRequests = $friendRequestRepository->findBy($criteria);

        return $this->json(['code' => 'SUCCESS', 'friendRequests' => $friendRequests], 200, [], ['groups' => ['friendRequest:read']]);
    }
}
