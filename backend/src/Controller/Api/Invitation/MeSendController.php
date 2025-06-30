<?php

namespace App\Controller\Api\Invitation;

use App\Entity\Invitation;
use App\Entity\User;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\UserRepository;
use App\Repository\GroupRepository;
use App\Repository\InvitationRepository;

//send an invitation to another user to join the current user's group
class MeSendController extends AbstractController
{
    private int $maxGroupUsers;
    public function __construct(ParameterBagInterface $params)
    {
        $this->maxGroupUsers = $params->get('app.max_group_users');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, UserRepository $userRepository, GroupRepository $groupRepository, InvitationRepository $invitationRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $targetUserId = $data['user_id'] ?? null;

        if (!$targetUserId) {
            return $this->json(['error' => 'Missing user_id'], 400);
        }

        if ($targetUserId == $user->getId()) {
            return $this->json(['error' => 'Cannot invite yourself'], 400);
        }

        $targetUser = $userRepository->find($targetUserId);
        if (!$targetUser) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $group = $groupRepository->findActiveGroupForUser($user);
        if (!$group) {
            return $this->json(['error' => 'You are not in a group'], 400);
        }

        if ($group->getUsers()->contains($targetUser)) {
            return $this->json(['error' => 'User is already in the group'], 400);
        }

        if (count($group->getUsers()) >= $this->maxGroupUsers) {
            return $this->json(['error' => 'Group is at max capacity'], 400);
        }

        $existing = $invitationRepository->findOneBy([
            'group' => $group,
            'invitedBy' => $user,
            'invitedUser' => $targetUser,
            'acceptedAt' => null,
            'revokedAt' => null,
            'deniedAt' => null
        ]);

        if ($existing) {
            return $this->json(['error' => 'Invitation already sent'], 400);
        }

        $invitation = new Invitation();
        $invitation->setGroup($group);
        $invitation->setInvitedBy($user);
        $invitation->setInvitedUser($targetUser);

        $entityManager->persist($invitation);
        $entityManager->flush();
        
        return $this->json(['message' => 'Invitation sent'], 201);
    }
}
