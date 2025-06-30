<?php

namespace App\Controller\Api\Group;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;

//kick a user from the current group (only by group creator)
class MeKickUserController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, GroupRepository $groupRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $targetUserId = $data['user_id'] ?? null;

        if (!$targetUserId) {
            return $this->json(['error' => 'Missing user_id'], 400);
        }

        if ($targetUserId == $user->getId()) {
            return $this->json(['error' => 'You cannot kick yourself'], 400);
        }

        $group = $groupRepository->findActiveGroupForUser($user);
        if (!$group) {
            return $this->json(['error' => 'You are not in a group'], 400);
        }

        if ($group->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Only the group owner can kick users'], 403);
        }

        $targetUser = $userRepository->find($targetUserId);
        if (!$targetUser) {
            return $this->json(['error' => 'User not found'], 404);
        }

        if (!$group->getUsers()->contains($targetUser)) {
            return $this->json(['error' => 'User is not in your group'], 400);
        }

        $group->removeUser($targetUser);
        
        $entityManager->flush();
        
        return $this->json(['message' => 'User kicked from group'], 200);
    }
}
