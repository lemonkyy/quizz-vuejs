<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @return Room|null
     */
    public function findActiveRoomForUser(User $user): ?Room
    {
        $result = $this->createQueryBuilder('g')
        ->join('g.users', 'u')
        ->where('u = :user')
        ->andWhere('g.deletedAt IS NULL')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();

        return $result[0] ?? null;
    }

    /**
     * @return Room[]
     */
    public function findAllRoomsForUser(User $user): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.users', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Room[]
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
