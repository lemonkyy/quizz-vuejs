<?php

namespace App\Controller\Api\Group;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

//create a new group
class MeCreateController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, GroupRepository $groupRepository): Response
    {
        $user = $this->getUser();
        
        $data = json_decode($request->getContent(), true);
        $isPublic = $data['isPublic'] ?? false;
        
        $code = "";
        //random unique code for group, should maybe be done differently
        do {
            $code = bin2hex(random_bytes(8));
            $existing = $groupRepository->findOneBy(['code' => $code, 'deletedAt' => null]);
        } while ($existing);

        $group = new Group();
        $group->setOwner($user);
        $group->addUser($user);
        $group->setCode($code);
        $group->setIsPublic($isPublic);

        $entityManager->persist($group);
        $entityManager->flush();

        return $this->json($group, 201, [], ['groups' => ['group:read']]);
    }
}
