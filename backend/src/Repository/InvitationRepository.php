<?php

namespace App\Repository;

use App\Entity\Invitation;
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

    /**
     * @return Invitation[]
     * find pending invites for user, may also be filtered by sender user
     */
    public function findActiveForUser(User $invitedUser, ?User $SenderUser = null): array
    {
        $expiredThreshold = new \DateTimeImmutable('-' . $this->inviteExpirationThreshold);

        $qb = $this->createQueryBuilder('i')
            ->where('i.invitedUser = :user')
            ->andWhere('i.revokedAt IS NULL')
            ->andWhere('i.deniedAt IS NULL')
            ->andWhere('i.acceptedAt IS NULL')
            ->andWhere('i.invitedAt >= :minDate')
            ->setParameter('user', $invitedUser)
            ->setParameter('minDate', $expiredThreshold);

        if ($SenderUser) {
            $qb->andWhere('i.invitedBy = :sender')
               ->setParameter('sender', $SenderUser);
        }

        return $qb->getQuery()->getResult();
    }
}
