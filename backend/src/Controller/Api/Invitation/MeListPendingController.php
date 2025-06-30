<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Repository\InvitationRepository;

//list the user's invitations received & available
class MeListPendingController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(InvitationRepository $invitationRepository): Response
    {
        $user = $this->getUser();
        
        $invitations = $invitationRepository->findActiveForUser($user);

        return $this->json($invitations, 200, [], ['groups' => ['invitation:read']]);
    }
}
