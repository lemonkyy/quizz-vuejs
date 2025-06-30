<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InvitationRepository;
use App\Repository\GroupRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//accept an invite
class AcceptController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(int $id, InvitationRepository $invitationRepository, GroupRepository $groupRepository, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response {
        
        $user = $this->getUser();

        $invitation = $invitationRepository->find($id);

        if (!$invitation || $invitation->getInvitedUser() !== $user) {
            return $this->json(['error' => 'Invitation not found'], 404);
        }

        $expirationThreshold = $params->get('app.invite_expiration_threshold');
        $expirationDate = (clone $invitation->getInvitedAt())->modify($expirationThreshold);
        $now = new \DateTimeImmutable();
        
        if ($now > $expirationDate) {
            return $this->json(['error' => 'Invitation has expired'], 400);
        }

        if ($invitation->getRevokedAt() !== null) {
            return $this->json(['error' => 'Invitation has been revoked'], 400);
        }

        $invitation->setAcceptedAt($now);
        $group = $invitation->getGroup();

        if ($group->getDeletedAt() !== null) {
            return $this->json(['error' => 'Group has been deleted'], 400);
        }

        $group->addUser($user);

        $entityManager->persist($group);
        $entityManager->persist($invitation);
        $entityManager->flush();

        return $this->json(['message' => 'Invitation accepted'], 200);
    }
}
