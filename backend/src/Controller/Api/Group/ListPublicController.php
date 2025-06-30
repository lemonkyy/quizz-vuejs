<?php

namespace App\Controller\Api\Group;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//list public joinable groups
class ListPublicController extends AbstractController
{
    private int $maxGroupUsers;

    public function __construct(ParameterBagInterface $params)
    {
        $this->maxGroupUsers = $params->get('app.max_group_users');
    }

    public function __invoke(GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->findPublicWithAvailableSlots($this->maxGroupUsers);
        return $this->json($groups, 200, [], ['groups' => ['group:read']]);
    }
}
