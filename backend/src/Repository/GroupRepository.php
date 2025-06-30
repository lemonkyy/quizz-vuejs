<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(Group::class, $registry);
    }

    /**
     * @return Group|null
     */
    public function findActiveGroupForUser(User $user): ?Group
    {
        return $this->createQueryBuilder('g')
            ->join('g.users', 'u')
            ->where('u = :user')
            ->andWhere('g.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Group[]
     */
    public function findAllGroupsForUser(User $user): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.users', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Group[]
     */
    public function findPublicWithAvailableSlots(int $maxUsers): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.isPublic = true')
            ->andWhere('(SELECT COUNT(u) FROM g.users u) < :max')
            ->setParameter('max', $maxUsers)
            ->getQuery()
            ->getResult();
    }
}
