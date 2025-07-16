<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByUsernamePaginated(string $username, ?int $page = 1, ?int $limit = 50): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('PARTIAL u.{id, username}', 'pp')
            ->leftJoin('u.profilePicture', 'pp')
            ->where('LOWER(unaccent(u.username)) LIKE LOWER(unaccent(:username))')
            ->setParameter('username', '%' . $username . '%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
    }

    public function findFriendsByUserIdPaginated(string $userId, ?int $page = 1, ?int $limit = 50, ?string $username = null): Paginator
    {
        $qb = $this->createQueryBuilder('u')
            ->select('PARTIAL u.{id, username}', 'pp')
            ->leftJoin('u.profilePicture', 'pp')
            ->innerJoin('u.friends', 'f')
            ->where('f.id = :userId')
            ->setParameter('userId', $userId);

        if ($username) {
            $qb->andWhere('LOWER(unaccent(u.username)) LIKE LOWER(unaccent(:username))')
                ->setParameter('username', '%' . $username . '%');
        }

        $qb->orderBy('u.username', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }
}
