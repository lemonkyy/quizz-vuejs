<?php

namespace App\EventListener;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\Collections\Collection;

class GroupMembershipListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        //this is to enforce 3 things:
        //1. A user can only be in one group at a time
        //2. If the owner of a group leaves, another user is promoted to owner
        //3. If a group has no users, it is soft deleted
        
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Group) {
                $this->enforceSingleGroupMembership($entity, $entityManager, $unitOfWork);
                $this->handleOwnerLeaving($entity, $entityManager, $unitOfWork);
                $this->handleGroupSoftDelete($entity, $entityManager, $unitOfWork);
            }
        }
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Group) {
                $this->enforceSingleGroupMembership($entity, $entityManager, $unitOfWork);
            }
        }
    }

    private function enforceSingleGroupMembership(Group $group, $entityManager, $unitOfWork)
    {
        foreach ($group->getUsers() as $user) {
            $repo = $entityManager->getRepository(Group::class);
            $oldGroups = $repo->createQueryBuilder('g')
                ->join('g.users', 'u')
                ->where('u = :user')
                ->andWhere('g != :group')
                ->andWhere('g.deletedAt IS NULL')
                ->setParameter('user', $user)
                ->setParameter('group', $group)
                ->getQuery()->getResult();
            foreach ($oldGroups as $oldGroup) {
                $oldGroup->removeUser($user);
                $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(Group::class), $oldGroup);
            }
        }
    }

    private function handleOwnerLeaving(Group $group, $entityManager, $unitOfWork)
    {
        $owner = $group->getOwner();
        if ($owner && !$group->getUsers()->contains($owner)) {
            $users = $group->getUsers();
            if (count($users) > 0) {
                $newOwner = $users->first();
                $group->setOwner($newOwner);
            } else {
                $group->setOwner(null);
            }
            $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(Group::class), $group);
        }
    }

    private function handleGroupSoftDelete(Group $group, $entityManager, $unitOfWork)
    {
        if (count($group->getUsers()) === 0 && $group->getDeletedAt() === null) {
            $group->setDeletedAt(new \DateTimeImmutable());
            $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(Group::class), $group);
        }
    }

    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }
}
