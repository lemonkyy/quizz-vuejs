<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FriendRequest>
 *
 * @method FriendRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendRequest[]    findAll()
 * @method FriendRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }

    public function countActiveForUser(User $user): int
    {
        return $this->createQueryBuilder('fr')
            ->select('count(fr.id)')
            ->where('fr.receiver = :user')
            ->andWhere('fr.revokedAt IS NULL')
            ->andWhere('fr.deniedAt IS NULL')
            ->andWhere('fr.acceptedAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllActiveForUser(User $user): array
    {
        return $this->createQueryBuilder('fr')
            ->where('fr.receiver = :user')
            ->andWhere('fr.revokedAt IS NULL')
            ->andWhere('fr.deniedAt IS NULL')
            ->andWhere('fr.acceptedAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}