<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ApiResource]
#[ORM\Entity]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sentFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receivedFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deniedAt = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
        $this->sentAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeImmutable $acceptedAt): self
    {
        $this->acceptedAt = $acceptedAt;
        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function setRevokedAt(?\DateTimeImmutable $revokedAt): self
    {
        $this->revokedAt = $revokedAt;
        return $this;
    }

    public function getDeniedAt(): ?\DateTimeImmutable
    {
        return $this->deniedAt;
    }

    public function setDeniedAt(?\DateTimeImmutable $deniedAt): self
    {
        $this->deniedAt = $deniedAt;
        return $this;
    }
}
