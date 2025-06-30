<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InvitationRepository;

//deny an invite
class MeDenyController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(int $id, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
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
