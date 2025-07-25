<?php

namespace App\Repository;

use App\Entity\Invitation;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InvitationRepository extends ServiceEntityRepository
{
    private string $inviteExpirationThreshold;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $params)
    {
        parent::__construct($registry, Invitation::class);
        $this->inviteExpirationThreshold = $params->get('app.invite_expiration_threshold');
    }

    public function countActiveForUser(User $invitedUser): int
    {
        $expiredThreshold = new \DateTimeImmutable('-' . $this->inviteExpirationThreshold . '');

        return $this->createQueryBuilder('i')
            ->select('count(i.id)')
            ->where('i.receiver = :user')
            ->andWhere('i.revokedAt IS NULL')
            ->andWhere('i.deniedAt IS NULL')
            ->andWhere('i.acceptedAt IS NULL')
            ->andWhere('i.sentAt >= :minDate')
            ->setParameter('user', $invitedUser)
            ->setParameter('minDate', $expiredThreshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findActiveForUser(User $invitedUser): array
    {
        $expiredThreshold = new \DateTimeImmutable('-' . $this->inviteExpirationThreshold . '');

        return $this->createQueryBuilder('i')
            ->where('i.receiver = :user')
            ->andWhere('i.revokedAt IS NULL')
            ->andWhere('i.deniedAt IS NULL')
            ->andWhere('i.acceptedAt IS NULL')
            ->andWhere('i.sentAt >= :minDate')
            ->setParameter('user', $invitedUser)
            ->setParameter('minDate', $expiredThreshold)
            ->getQuery()
            ->getResult();
    }

    public function findActiveInvitation(Room $room, User $sender, User $receiver): ?Invitation
    {
        $expiredThreshold = new \DateTimeImmutable('-' . $this->inviteExpirationThreshold . '');

        return $this->createQueryBuilder('i')
            ->where('i.room = :room')
            ->andWhere('i.sender = :sender')
            ->andWhere('i.receiver = :receiver')
            ->andWhere('i.acceptedAt IS NULL')
            ->andWhere('i.revokedAt IS NULL')
            ->andWhere('i.deniedAt IS NULL')
            ->andWhere('i.sentAt >= :minDate')
            ->setParameter('room', $room)
            ->setParameter('sender', $sender)
            ->setParameter('receiver', $receiver)
            ->setParameter('minDate', $expiredThreshold)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
