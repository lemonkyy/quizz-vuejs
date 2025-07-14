<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Api\Processor\Invitation\MeAcceptProcessor;
use App\Api\Processor\Invitation\MeCancelProcessor;
use App\Api\Processor\Invitation\MeDenyProcessor;
use App\Api\Processor\Invitation\MeSendProcessor;
use App\Api\Provider\Invitation\MeListPendingProvider;
use App\Api\Provider\Invitation\MeListSentProvider as InvitationMeListSentProvider;
use App\Repository\InvitationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/invitation/{id}/send',
            processor: MeSendProcessor::class,
            read: false,
            name: 'api_invitation_send'
        ),
        new Get(
            uriTemplate: '/invitation/sent',
            input: false,
            provider: InvitationMeListSentProvider::class,
            read: false,
            name: 'api_invitation_sent'
        ),
        new Get(
            uriTemplate: '/invitation/pending',
            input: false,
            provider: MeListPendingProvider::class,
            read: false,
            name: 'api_invitation_pending'
        ),

        new Post(
            uriTemplate: '/invitation/{id}/accept',
            input: false,
            processor: MeAcceptProcessor::class,
            read: false,
            name: 'api_invitation_accept'
        ),
        new Post(
            uriTemplate: '/invitation/{id}/deny',
            input: false,
            processor: MeDenyProcessor::class,
            read: false,
            name: 'api_invitation_deny'
        ),
        new Post(
            uriTemplate: '/invitation/{id}/cancel',
            input: false,
            processor: MeCancelProcessor::class,
            read: false,
            name: 'api_invitation_cancel'
        ),
    ]
)]

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[Groups(['invitation:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: Room::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $invitedBy = null;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $invitedUser = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $invitedAt = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $acceptedAt = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $revokedAt = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deniedAt = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
        $this->invitedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): self
    {
        $this->room = $room;
        return $this;
    }

    public function getInvitedBy(): ?User
    {
        return $this->invitedBy;
    }

    public function setInvitedBy(User $user): self
    {
        $this->invitedBy = $user;
        return $this;
    }

    public function getInvitedUser(): ?User
    {
        return $this->invitedUser;
    }

    public function setInvitedUser(User $user): self
    {
        $this->invitedUser = $user;
        return $this;
    }

    public function getInvitedAt(): \DateTimeImmutable
    {
        return $this->invitedAt;
    }

    public function setInvitedAt(\DateTimeImmutable $date): self
    {
        $this->invitedAt = $date;
        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeImmutable $date): self
    {
        $this->acceptedAt = $date;
        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function setRevokedAt(?\DateTimeImmutable $date): self
    {
        $this->revokedAt = $date;
        return $this;
    }

    public function getDeniedAt(): ?\DateTimeImmutable
    {
        return $this->deniedAt;
    }

    public function setDeniedAt(?\DateTimeImmutable $date): self
    {
        $this->deniedAt = $date;
        return $this;
    }
}
