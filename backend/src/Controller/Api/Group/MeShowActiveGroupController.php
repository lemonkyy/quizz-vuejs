<?php

namespace App\Controller\Api\Group;

use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GroupRepository;

//shows the active group of the current user
class MeShowActiveGroupController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, GroupRepository $groupRepository): Response
    {
        $user = $this->getUser();
        
        $group = $groupRepository->findActiveGroupForUser($user);
        
        return $this->json($group, 200, [], ['groups' => ['group:read']]);
    }
}
