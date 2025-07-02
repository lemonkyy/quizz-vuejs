<?php

namespace App\Controller\Api\Room;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\RoomRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//shows the active room of the current user
class MeShowCurrentController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, RoomRepository $roomRepository): Response
    {
        $room = $roomRepository->findActiveRoomForUser($user);

        return $this->json($room, 200, [], ['groups' => ['room:read']]);
    }
}
