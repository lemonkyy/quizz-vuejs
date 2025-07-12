<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\Api\FriendRequest\MeAcceptController;
use App\Controller\Api\FriendRequest\MeDenyController;
use App\Controller\Api\FriendRequest\MeCancelController;
use App\Controller\Api\FriendRequest\MeListSentController;
use App\Controller\Api\FriendRequest\MeListReceivedController;
use App\Controller\Api\FriendRequest\MeSendController;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/friend-request/{id}/send',
            controller: MeSendController::class,
            input: false,
            read: false,
            name: 'api_friend_requests_send',
            openapiContext: [
                'summary' => 'Send a friend request',
                'description' => 'Sends a friend request to another user.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                        'description' => 'The ID of the user to send the friend request to.'
                    ]
                ],
                'responses' => [
                    '201' => ['description' => 'Friend request sent'],
                    '400' => ['description' => 'Invalid request'],
                    '404' => ['description' => 'User not found'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        new Post(
            uriTemplate: '/friend-request/{id}/accept',
            controller: MeAcceptController::class,
            read: false,
            input: false,
            name: 'api_friend_requests_accept',
            openapiContext: [
                'summary' => 'Accept a friend request',
                'description' => 'Accepts a friend request by its ID.',
                'responses' => [
                    '200' => ['description' => 'Friend request accepted'],
                    '400' => ['description' => 'Invalid request'],
                    '404' => ['description' => 'Friend request not found'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        new Post(
            uriTemplate: '/friend-request/{id}/deny',
            controller: MeDenyController::class,
            read: false,
            input: false,
            name: 'api_friend_requests_deny',
            openapiContext: [
                'summary' => 'Deny a friend request',
                'description' => 'Denies a friend request by its ID.',
                'responses' => [
                    '200' => ['description' => 'Friend request denied'],
                    '400' => ['description' => 'Invalid request'],
                    '404' => ['description' => 'Friend request not found'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        new Post(
            uriTemplate: '/friend-request/{id}/cancel',
            controller: MeCancelController::class,
            read: false,
            name: 'api_friend_requests_cancel',
            openapiContext: [
                'summary' => 'Cancel a sent friend request',
                'description' => 'Cancels a sent friend request by its ID.',
                'responses' => [
                    '200' => ['description' => 'Friend request cancelled'],
                    '400' => ['description' => 'Invalid request'],
                    '404' => ['description' => 'Friend request not found'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        new Get(
            uriTemplate: '/friend-request/sent',
            controller: MeListSentController::class,
            read: false,
            input: false,
            name: 'api_friend_requests_sent',
            openapiContext: [
                'summary' => 'List sent friend requests',
                'description' => 'Lists all friend requests sent by the current user.',
                'responses' => [
                    '200' => ['description' => 'List of sent friend requests'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        new Get(
            uriTemplate: '/friend-request/received',
            controller: MeListReceivedController::class,
            read: false,
            input: false,
            name: 'api_friend_requests_received',
            openapiContext: [
                'summary' => 'List received friend requests',
                'description' => 'Lists all friend requests received by the current user.',
                'responses' => [
                    '200' => ['description' => 'List of received friend requests'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
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
