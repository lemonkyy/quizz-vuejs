<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RoomRepository extends ServiceEntityRepository
{

    private string $maxRoomUsers;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $params)
    {
        parent::__construct($registry, Room::class);
        $this->maxRoomUsers = $params->get('app.max_room_users');
    }

    /**
     * @return Room|null
     */
    public function findActiveRoomForUser(User $user): ?Room
    {
        $result = $this->createQueryBuilder('r')
        ->join('r.users', 'u')
        ->where('u = :user')
        ->andWhere('r.deletedAt IS NULL')
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
        return $this->createQueryBuilder('r')
            ->join('r.users', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Room[]
     */
    public function findPublicWithAvailableSlots(): array
    {
        $maxUsers = $this->maxRoomUsers;
        
        return $this->createQueryBuilder('r')
            ->leftJoin('r.users', 'u')
            ->where('r.isPublic = true')
            ->groupBy('r.id')
            ->having('COUNT(u) < :max')
            ->setParameter('max', $maxUsers)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Room|null
     */
    public function findRoomByUserId(string $userId): ?Room
    {
        $result = $this->createQueryBuilder('r')
            ->join('r.users', 'u')
            ->where('u.id = :userId')
            ->andWhere('r.deletedAt IS NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();

        return $result[0] ?? null;
    }
}
