<?php

namespace App\Controller\Api\Group;

use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GroupRepository;

//current user leaves their group
class MeLeaveController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, GroupRepository $groupRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $group = $groupRepository->findActiveGroupForUser($user);

        if (!$group) {
            return $this->json(['error' => 'User is not in an active group'], 400);
        }

        $group->removeUser($user);

        $entityManager->persist($group);
        $entityManager->flush();

        return $this->json(['message' => 'Left group successfully'], 200);
    }
}
