<?php

namespace App\Service;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

//service to make sure a user can only be in one room at a time, and to handle room ownership changes and soft deletion
class RoomMembershipService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    //this is logic for when a user leaves a room
    public function handleUserLeavingRoom(User $user): void
    {
        $roomRepository = $this->entityManager->getRepository(Room::class);

        $currentRoom = $roomRepository->findRoomByUserId($user->getId());

        if (null === $currentRoom) {
            return;
        }

        $this->leaveRoom($user, $currentRoom);
    }

    public function leaveRoom(User $user, Room $room): void
    {
        $room->removeUser($user);

        if ($room->getOwner() === $user) {
            $this->handleOwnerLeaving($room);
        }

        if ($room->getUsers()->isEmpty()) {
            $this->handleRoomSoftDelete($room);
        }

        $this->entityManager->flush();
    }

    //transfer ownership of room to another user
    private function handleOwnerLeaving(Room $room): void
    {
        $users = $room->getUsers();

        if (count($users) > 0) {
            $newOwner = $users->first();
            $room->setOwner($newOwner);
        }
    }

    //room with no useres is soft deleted
    private function handleRoomSoftDelete(Room $room): void
    {
        if (count($room->getUsers()) === 0 && $room->getDeletedAt() === null) {
            $room->setDeletedAt(new \DateTimeImmutable());
        }
    }
}
