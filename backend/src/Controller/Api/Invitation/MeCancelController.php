<?php

namespace App\Controller\Api\Invitation;

use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//cancel an invitation by its id
class MeCancelController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager): Response
    {
        $invitation = $invitationRepository->find($id);

        if (!$invitation || $invitation->getInvitedBy() !== $user) {
            return $this->json(['error' => 'Invitation not found or not allowed'], 404);
        }

        $invitation->setRevokedAt(new \DateTimeImmutable());

        $entityManager->persist($invitation);
        $entityManager->flush();

        return $this->json(['message' => 'Invitation revoked'], 200);
    }
}
