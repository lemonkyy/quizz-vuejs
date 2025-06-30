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

//current user joins a group by code
class MeJoinController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, GroupRepository $groupRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? null;

        if (!$code) {
            return $this->json(['error' => 'Group code is required'], 400);
        }

        $group = $groupRepository->findOneBy(['code' => $code]);

        if (!$group) {
            return $this->json(['error' => 'Group not found'], 404);
        }
        
        if ($group->getDeletedAt() !== null) {
            return $this->json(['error' => 'Group has been deleted'], 400);
        }

        $group->setOwner($user);
        $group->addUser($user);
        
        $entityManager->persist($group);
        $entityManager->flush();

        return $this->json($group, 200, [], ['groups' => ['group:read']]);
    }
}
