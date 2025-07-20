<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
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
            ->join('r.roomPlayers', 'rp')
            ->join('rp.player', 'u')
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
    public function findPublicWithAvailableSlots(?int $page = 1, ?int $limit = 25): Paginator
    {
        $maxUsers = $this->maxRoomUsers;
        
        $qb =  $this->createQueryBuilder('r')
            ->leftJoin('r.roomPlayers', 'rp')
            ->where('r.isPublic = true')
            ->groupBy('r.id')
            ->having('COUNT(rp) < :max')
            ->andHaving('COUNT(rp) > 0')
            ->setParameter('max', $maxUsers)
            ->orderBy('r.createdAt', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    /**
     * @return Room|null
     */
    public function findRoomByRoomPlayerId(string $roomPlayerId): ?Room
    {
        return $this->createQueryBuilder('r')
            ->join('r.roomPlayers', 'rp')
            ->where('rp.id = :rpId')
            ->andWhere('r.deletedAt IS NULL')
            ->setParameter('rpId', $roomPlayerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Room|null
     */
    public function findByCode(string $code): ?Room
    {
        return $this->createQueryBuilder('r')
            ->where('r.code = :code')
            ->andWhere('r.deletedAt IS NULL')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
