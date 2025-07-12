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

    public function findByUsernamePaginated(string $username, ?int $page = 1, ?int $limit = 50): array
    {
        $offset = ($page - 1) * $limit;
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT DISTINCT u.id, u.username, pp.file_name as "profilePictureUrl"
            FROM "user" u
            LEFT JOIN profile_picture pp ON u.profile_picture_id = pp.id
            WHERE LOWER(public.unaccent(u.username)) LIKE LOWER(public.unaccent(:username))
            LIMIT :limit OFFSET :offset
            ';
        $resultSet = $conn->executeQuery($sql, ['username' => '%' . $username . '%', 'limit' => $limit, 'offset' => $offset]);
        return $resultSet->fetchAllAssociative();
    }

    public function findFriendsByUserIdPaginated(string $userId, ?int $page = 1, ?int $limit = 50, ?string $username = null): array
    {
        $offset = ($page - 1) * $limit;
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DISTINCT u.id, u.username, pp.file_name as "profilePictureUrl"
            FROM "user" u
            JOIN user_friends uf ON (u.id = uf.friend_user_id AND uf.user_id = :userId) OR (u.id = uf.user_id AND uf.friend_user_id = :userId)
            LEFT JOIN profile_picture pp ON u.profile_picture_id = pp.id
        ';

        $params = ['userId' => $userId];

        if ($username) {
            $sql .= ' WHERE LOWER(public.unaccent(u.username)) LIKE LOWER(public.unaccent(:username))';
            $params['username'] = '%' . $username . '%';
        }

        $sql .= ' ORDER BY u.username ASC LIMIT :limit OFFSET :offset';

        $resultSet = $conn->executeQuery($sql, array_merge($params, ['limit' => $limit + 1, 'offset' => $offset]));
        $friends = $resultSet->fetchAllAssociative();

        $hasMore = count($friends) > $limit;
        if ($hasMore) {
            array_pop($friends);
        }

        return [
            'friends' => $friends,
            'hasMore' => $hasMore,
        ];
    }
}
