<?php

namespace App\Controller\Api\Room;

use App\Entity\Room;
use App\Entity\RoomPlayer;
use App\Service\RoomMembershipService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;

//create a new room
class MeCreateController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, Request $request, RoomMembershipService $roomMembershipService): Response
    {
        $data = json_decode($request->getContent(), true);
        $isPublic = $data['isPublic'] ?? false;

        $room = new Room($roomMembershipService->handleUserCreatingRoom($user, $isPublic));

        return $this->json(['code' => 'SUCCESS', 'room' => $room], 201, [], ['groups' => ['room:read']]);
    }
}
