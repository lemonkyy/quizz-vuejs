<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//list invitations sent by the current user that are still pending. May filter a specific user's invitation
class MeListSentController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $targetUserId = $request->query->get('user_id');

        $criteria = [
            'invitedBy' => $user,
            'acceptedAt' => null,
            'deniedAt' => null,
            'revokedAt' => null
        ];

        if ($targetUserId) {
            $criteria['invitedUser'] = $targetUserId;
        }

        $invitations = $entityManager->getRepository(Invitation::class)->findBy($criteria);

        return $this->json(['code' => 'SUCCESS', 'invitations' => $invitations], 200, [], ['groups' => ['invitation:read']]);
    }
}
