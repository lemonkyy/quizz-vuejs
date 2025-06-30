<?php

namespace App\Controller\Api\Invitation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//deny an invite
class MeDenyController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager): Response
    {
        $invitation = $invitationRepository->find($id);

        if (!$invitation || $invitation->getInvitedUser() !== $user) {
            return $this->json(['error' => 'Invitation not found'], 404);
        }

        $invitation->setDeniedAt(new \DateTimeImmutable());

        $entityManager->persist($invitation);
        $entityManager->flush();

        return $this->json(['message' => 'Invitation denied'], 200);
    }
}
