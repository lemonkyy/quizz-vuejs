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
            input: false,
            processor: MeSendProcessor::class,
            name: 'api_invitation_send'
        ),
        new Get(
            uriTemplate: '/invitation/sent',
            input: false,
            provider: InvitationMeListSentProvider::class,
            normalizationContext: ['groups' => ['invitation:read']],
            name: 'api_invitation_sent'
        ),
        new Get(
            uriTemplate: '/invitation/pending',
            input: false,
            provider: MeListPendingProvider::class,
            normalizationContext: ['groups' => ['invitation:read']],
            name: 'api_invitation_pending'
        ),
        new Post(
            uriTemplate: '/invitation/{id}/accept',
            input: false,
            processor: MeAcceptProcessor::class,
            normalizationContext: ['groups' => ['invitation:read']],
            name: 'api_invitation_accept'
        ),
        new Post(
            uriTemplate: '/invitation/{id}/deny',
            input: false,
            processor: MeDenyProcessor::class,
            normalizationContext: ['groups' => ['invitation:read']],
            name: 'api_invitation_deny'
        ),
        new Post(
            uriTemplate: '/invitation/{id}/cancel',
            input: false,
            processor: MeCancelProcessor::class,
            normalizationContext: ['groups' => ['invitation:read']],
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
    private ?User $sender = null;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $sentAt = null;

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
        $this->sentAt = new \DateTimeImmutable();
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

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(User $user): self
    {
        $this->sender = $user;
        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(User $user): self
    {
        $this->receiver = $user;
        return $this;
    }

    public function getSentAt(): \DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $date): self
    {
        $this->sentAt = $date;
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
