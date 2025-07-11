<?php

namespace App\Service;

use App\Entity\Room;
use App\Entity\RoomPlayer;
use App\Entity\User;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

//service to make sure a user can only be in one room at a time, and to handle room ownership changes and soft deletion
class RoomMembershipService
{
    private EntityManagerInterface $entityManager;
    private RoomRepository $roomRepository;
    private RoomCodeGenerationService $roomCodeGenerationService;

    public function __construct(EntityManagerInterface $entityManager, RoomRepository $roomRepository, RoomCodeGenerationService $roomCodeGenerationService)
    {
        $this->entityManager = $entityManager;
        $this->roomRepository = $roomRepository;
        $this->roomCodeGenerationService = $roomCodeGenerationService;
    }

    public function handleUserCreatingRoom(User $user, Bool $isPublic = true): Room 
    {
        $this->handleUserLeavingRoom($user);

        $room = new Room();
        $roomPlayer = new RoomPlayer($user, $room);
        
        $room->setOwner($user);
        $room->setIsPublic($isPublic);
        $room->setCode($this->roomCodeGenerationService->generateUniqueRoomCode());

        $this->entityManager->persist($room);
        $this->entityManager->persist($roomPlayer);
        $this->entityManager->flush();

        return $room;
    }

    public function handleUserJoiningRoom(User $user, Room $room): void
    {
        $this->handleUserLeavingRoom($user);

        $roomPlayer = new RoomPlayer($user, $room);

        $this->entityManager->persist($roomPlayer);
        $this->entityManager->flush();
    }

    //this is logic for when the logged user leaves his current room
    public function handleUserLeavingRoom(User $user): void
    {
        $roomPlayer = $user->getRoomPlayer();

        if (null === $roomPlayer) {
            return;
        }

        $currentRoom = $this->roomRepository->findRoomByRoomPlayerId($roomPlayer->getId());

        if (null === $currentRoom) {
            return;
        }

        $this->leaveRoom($user, $currentRoom);
    }

    public function handleUserKickedFromRoom(User $user, Room $room): void
    {
        $room->removeRoomPlayer($user->getRoomPlayer());

        $this->entityManager->persist($room);
        $this->entityManager->flush();
    }

    public function handleUserDeletingRoom(Room $room): void
    {
        $room->setDeletedAt(new \DateTimeImmutable());

        //remove all users from the room
        foreach ($room->getRoomPlayers() as $roomPlayer) {
            $room->removeRoomPlayer($roomPlayer);
        }

        $this->entityManager->persist($room);
        $this->entityManager->flush();
    }

    public function leaveRoom(User $user, Room $room): void
    {
        $room->removeRoomPlayer($user->getRoomPlayer());
        $user->setRoomPlayer(null);

        if ($room->getOwner() === $user) {
            $this->handleOwnerLeaving($room);
        }
        
        if ($room->getRoomPlayers()->isEmpty()) {
            $this->handleRoomSoftDelete($room);
        }

        $this->entityManager->flush();
    }

    //transfer ownership of room to another user
    private function handleOwnerLeaving(Room $room): void
    {
        $roomPlayers = $room->getRoomPlayers();

        if (count($roomPlayers) > 0) {
            $newOwner = $roomPlayers->first()->getPlayer();
            $room->setOwner($newOwner);
        }
    }

    //room with no users is soft deleted
    private function handleRoomSoftDelete(Room $room): void
    {
        if (count($room->getRoomPlayers()) === 0 && $room->getDeletedAt() === null) {
            $room->setDeletedAt(new \DateTimeImmutable());
        }
    }
}
