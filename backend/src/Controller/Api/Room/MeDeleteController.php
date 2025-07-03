<?php

namespace App\Controller\Api\Room;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoomRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//soft delete the room the current user is creator of
class MeDeleteController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {

        $room = $roomRepository->findOneBy(['owner' => $user, 'deletedAt' => null]);

        if (!$room) {
            return $this->json(['error' => 'No active room found for user as owner'], 404);
        }

        $room->setDeletedAt(new \DateTimeImmutable());

        // Remove all users from the room
        foreach ($room->getUsers() as $userInRoom) {
            $room->removeUser($userInRoom);
        }

        $entityManager->persist($room);
        $entityManager->flush();

        return $this->json(['message' => 'Room deleted'], 200);
    }
}
