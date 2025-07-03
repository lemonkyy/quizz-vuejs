<?php

namespace App\Controller\Api\Invitation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use App\Service\RoomMembershipService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//accept an invite
class MeAcceptController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager, ParameterBagInterface $params, RoomMembershipService $roomMembershipService): Response {

        $invitation = $invitationRepository->find($id);

        if (!$invitation || $invitation->getInvitedUser() !== $user) {
            return $this->json(['error' => 'Invitation not found'], 404);
        }

        $expirationThreshold = $params->get('app.invite_expiration_threshold');
        $expirationDate = (clone $invitation->getInvitedAt())->modify('+' . $expirationThreshold);
        $now = new \DateTimeImmutable();

        if ($now > $expirationDate) {
            return $this->json(['error' => 'Invitation has expired'], 400);
        }

        if ($invitation->getRevokedAt() !== null) {
            return $this->json(['error' => 'Invitation has been revoked'], 400);
        }

        $invitation->setAcceptedAt($now);
        $room = $invitation->getRoom();

        if ($room->getDeletedAt() !== null) {
            return $this->json(['error' => 'Room has been deleted'], 400);
        }

        $roomMembershipService->handleUserLeavingRoom($user);

        $room->addUser($user);

        $entityManager->persist($room);
        $entityManager->persist($invitation);
        $entityManager->flush();

        return $this->json(['message' => 'Invitation accepted'], 200);
    }
}
