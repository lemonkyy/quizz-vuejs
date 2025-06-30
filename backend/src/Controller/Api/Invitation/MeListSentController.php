<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//list invitations sent by the current user that are still pending. May filter a specific user's invitation
class MeListSentController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

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

        return $this->json($invitations, 200, [], ['groups' => ['invitation:read']]);
    }
}
