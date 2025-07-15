<?php

namespace App\Repository;

use App\Api\Dto\Notification\NotificationDto;
use App\Entity\User;
use App\Enum\NotificationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NotificationRepository extends ServiceEntityRepository
{
    private string $inviteExpirationThreshold;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $params)
    {
        parent::__construct($registry, User::class);
        $this->inviteExpirationThreshold = $params->get('app.invite_expiration_threshold');
    }

    /**
     * @throws Exception
     */
    public function getNotifications(User $user, int $page, int $limit): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('type', 'type');
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('sendAt', 'sendAt');
        $rsm->addScalarResult('sender_id', 'sender_id');
        $rsm->addScalarResult('sender_username', 'sender_username');
        $rsm->addScalarResult('sender_profile_picture', 'sender_profile_picture');
        $rsm->addScalarResult('room_id', 'room_id');
        $rsm->addScalarResult('room_name', 'room_name');

        $expiredThreshold = new \DateTimeImmutable('-' . $this->inviteExpirationThreshold . '');

        $sql = '''
            SELECT 'friend_request' as type, fr.id, fr.sent_at as sendAt, fr_sender.id as sender_id, fr_sender.username as sender_username, pp.file_name as sender_profile_picture, null as room_id, null as room_name
            FROM friend_request fr
            JOIN "user" fr_sender ON fr.sender_id = fr_sender.id
            LEFT JOIN profile_picture pp ON fr_sender.profile_picture_id = pp.id
            WHERE fr.receiver_id = :userId AND fr.accepted_at IS NULL AND fr.denied_at IS NULL AND fr.revoked_at IS NULL

            UNION ALL

            SELECT 'invitation' as type, i.id, i.invited_at as sendAt, i_sender.id as sender_id, i_sender.username as sender_username, pp.file_name as sender_profile_picture
            FROM invitation i
            JOIN "user" i_sender ON i.invited_by_id = i_sender.id
            LEFT JOIN profile_picture pp ON i_sender.profile_picture_id = pp.id
            WHERE i.invited_user_id = :userId AND i.accepted_at IS NULL AND i.denied_at IS NULL AND i.revoked_at IS NULL AND i.sent_at >= :minDate

            ORDER BY sendAt DESC
            LIMIT :limit OFFSET :offset
        ''';

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('userId', $user->getId());
        $query->setParameter('minDate', $expiredThreshold);
        $query->setParameter('limit', $limit);
        $query->setParameter('offset', ($page - 1) * $limit);

        $results = $query->getResult();

        $notifications = [];
        foreach ($results as $result) {
            $notificationType = NotificationType::from($result['type']);
            $data = [
                'sender' => [
                    'id' => $result['sender_id'],
                    'username' => $result['sender_username'],
                    'profilePicture' => $result['sender_profile_picture'],
                ],
            ];

            $notifications[] = new NotificationDto(
                $notificationType,
                $result['id'],
                new \DateTimeImmutable($result['sendAt']),
                $data
            );
        }

        return $notifications;
    }

    /**
     * @throws Exception
     */
    public function countNotifications(User $user): int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');

        $expiredThreshold = new \DateTimeImmutable('-' . $this->inviteExpirationThreshold . '');

        $sql = '''
            SELECT COUNT(*) as count
            FROM (
                SELECT fr.id FROM friend_request fr WHERE fr.receiver_id = :userId AND fr.accepted_at IS NULL AND fr.denied_at IS NULL AND fr.revoked_at IS NULL
                UNION ALL
                SELECT i.id FROM invitation i WHERE i.invited_user_id = :userId AND i.accepted_at IS NULL AND i.denied_at IS NULL AND i.revoked_at IS NULL AND i.sent_at >= :minDate
            ) as notifications
        ''';

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('userId', $user->getId());
        $query->setParameter('minDate', $expiredThreshold);

        return (int) $query->getSingleScalarResult();
    }
}
