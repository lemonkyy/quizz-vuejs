<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Api\Processor\FriendRequest\MeAcceptProcessor;
use App\Api\Processor\FriendRequest\MeDenyProcessor;
use App\Api\Processor\FriendRequest\MeCancelProcessor;
use App\Api\Processor\FriendRequest\MeSendProcessor;
use App\Api\Provider\FriendRequest\MeListSentProvider;
use App\Api\Provider\FriendRequest\MeListReceivedProvider;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/friend-request/{id}/send',
            processor: MeSendProcessor::class,
            input: false,
            normalizationContext: ['groups' => ['friendRequest:read']],
            name: 'api_friend_requests_send',
        ),
        new Post(
            uriTemplate: '/friend-request/{id}/accept',
            processor: MeAcceptProcessor::class,
            input: false,
            normalizationContext: ['groups' => ['friendRequest:read']],
            name: 'api_friend_requests_accept'
        ),
        new Post(
            uriTemplate: '/friend-request/{id}/deny',
            processor: MeDenyProcessor::class,
            input: false,
            normalizationContext: ['groups' => ['friendRequest:read']],
            name: 'api_friend_requests_deny'
        ),
        new Post(
            uriTemplate: '/friend-request/{id}/cancel',
            processor: MeCancelProcessor::class,
            input: false,
            normalizationContext: ['groups' => ['friendRequest:read']],
            name: 'api_friend_requests_cancel'
        ),
        new Get(
            uriTemplate: '/friend-request/sent',
            provider: MeListSentProvider::class,
            input: false,
            normalizationContext: ['groups' => ['friendRequest:read']],
            name: 'api_friend_requests_sent'
        ),
        new Get(
            uriTemplate: '/friend-request/received',
            provider: MeListReceivedProvider::class,
            input: false,
            normalizationContext: ['groups' => ['friendRequest:read']],
            name: 'api_friend_requests_received'
        )
    ]
)]
#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[Groups(['friendRequest:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[Groups(['friendRequest:read'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sentFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[Groups(['friendRequest:read'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receivedFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[Groups(['friendRequest:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $sentAt = null;

    #[Groups(['friendRequest:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $acceptedAt = null;

    #[Groups(['friendRequest:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    #[Groups(['friendRequest:read'])]
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

