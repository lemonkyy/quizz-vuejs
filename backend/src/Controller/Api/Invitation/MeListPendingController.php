<?php

namespace App\Controller\Api\Invitation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\InvitationRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//list the user's invitations received & available
class MeListPendingController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, InvitationRepository $invitationRepository): Response
    {
        $invitations = $invitationRepository->findActiveForUser($user);

        return $this->json($invitations, 200, [], ['groups' => ['invitation:read']]);
    }
}
