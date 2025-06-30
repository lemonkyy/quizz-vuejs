<?php

namespace App\EventListener;

use App\Entity\Room;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;

class RoomMembershipListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getObjectManager();

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $unitOfWork = $entityManager->getUnitOfWork();

        //this is to enforce 3 things:
        //1. A user can only be in one room at a time
        //2. If the owner of a room leaves, another user is promoted to owner
        //3. If a room has no users, it is soft deleted

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Room) {
                $this->enforceSingleRoomMembership($entity, $entityManager, $unitOfWork);
                $this->handleOwnerLeaving($entity, $entityManager, $unitOfWork);
                $this->handleRoomSoftDelete($entity, $entityManager, $unitOfWork);
            }
        }
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Room) {
                $this->enforceSingleRoomMembership($entity, $entityManager, $unitOfWork);
            }
        }
    }

    private function enforceSingleRoomMembership(Room $room, $entityManager, $unitOfWork)
    {
        foreach ($room->getUsers() as $user) {

            $repo = $entityManager->getRepository(Room::class);

            $oldRooms = $repo->createQueryBuilder('g')
                ->join('g.users', 'u')
                ->where('u = :user')
                ->andWhere('g != :room')
                ->andWhere('g.deletedAt IS NULL')
                ->setParameter('user', $user)
                ->setParameter('room', $room)
                ->getQuery()->getResult();

            foreach ($oldRooms as $oldRoom) {
                $oldRoom->removeUser($user);
                $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(Room::class), $oldRoom);
            }
        }
    }

    private function handleOwnerLeaving(Room $room, $entityManager, $unitOfWork)
    {

        $owner = $room->getOwner();

        if ($owner && !$room->getUsers()->contains($owner)) {
            $users = $room->getUsers();

            if (count($users) > 0) {
                $newOwner = $users->first();
                $room->setOwner($newOwner);
            }

            $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(Room::class), $room);
        }
    }

    private function handleRoomSoftDelete(Room $room, $entityManager, $unitOfWork)
    {
        if (count($room->getUsers()) === 0 && $room->getDeletedAt() === null) {
            $room->setDeletedAt(new \DateTimeImmutable());
            $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(Room::class), $room);
        }
    }

    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }
}
