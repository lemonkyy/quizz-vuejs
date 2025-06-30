<?php

namespace App\Controller\Api\Room;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoomRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//current user leaves their room
class MeLeaveController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {
        $room = $roomRepository->findActiveRoomForUser($user);

        if (!$room) {
            return $this->json(['error' => 'User is not in an active room'], 400);
        }

        $room->removeUser($user);

        $entityManager->persist($room);
        $entityManager->flush();

        return $this->json(['message' => 'Left room successfully'], 200);
    }
}
