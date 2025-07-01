<?php

namespace App\Controller\Api\Room;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoomRepository;
use App\Service\RoomMembershipService;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//current user joins a room by code
class MeJoinController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, RoomRepository $roomRepository, EntityManagerInterface $entityManager, RoomMembershipService $roomMembershipService): Response
    {
        $room = $roomRepository->findOneBy(['id' => $id]);

        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        if ($room->getDeletedAt() !== null) {
            return $this->json(['error' => 'Room has been deleted'], 400);
        }

        if ($room->getUsers()->contains($user)) {
            return $this->json(['error' => 'User is already in this room'], 400);
        }

        $roomMembershipService->handleUserLeavingRoom($user);

        $room->addUser($user);

        $entityManager->persist($room);
        $entityManager->flush();

        return $this->json($room, 200, [], ['groups' => ['room:read']]);
    }
}
