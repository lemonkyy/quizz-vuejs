<?php

namespace App\Repository;

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

    public function findByUsername(string $username, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT u.id, u.username, pp.file_name as "profilePictureUrl"
            FROM "user" u
            LEFT JOIN profile_picture pp ON u.profile_picture_id = pp.id
            WHERE LOWER(public.unaccent(u.username)) LIKE LOWER(public.unaccent(:username))
            LIMIT :limit OFFSET :offset
            ';
        $resultSet = $conn->executeQuery($sql, ['username' => '%' . $username . '%', 'limit' => $limit, 'offset' => $offset]);
        return $resultSet->fetchAllAssociative();
    }
}
