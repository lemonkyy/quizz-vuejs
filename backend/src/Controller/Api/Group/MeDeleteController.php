<?php

namespace App\Controller\Api\Group;

use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\Request;

//soft delete the group the current user is creator of
class MeDeleteController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, GroupRepository $groupRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $group = $groupRepository->findOneBy(['owner' => $user, 'deletedAt' => null]);

        if (!$group) {
            return $this->json(['error' => 'No active group found for user as owner'], 404);
        }

        $group->setDeletedAt(new \DateTimeImmutable());
        
        $entityManager->persist($group);
        $entityManager->flush();

        return $this->json(['message' => 'Group deleted'], 200);
    }
}
