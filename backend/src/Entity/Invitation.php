<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Api\Invitation\MeSendController;
use App\Controller\Api\Invitation\MeListSentController;
use App\Controller\Api\Invitation\MeListPendingController;
use App\Controller\Api\Invitation\MeAcceptController;
use App\Controller\Api\Invitation\MeDenyController;
use App\Controller\Api\Invitation\MeCancelController;
use App\Repository\InvitationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/invitation/{id}/send',
            controller: MeSendController::class,
            read: false,
            name: 'api_invitation_send',
            openapiContext: [
                'summary' => 'Send a room invitation',
                'description' => 'Send an invitation to another user to join your current room. Only possible if you are in a room, the user is not already in the room, and the room is not full.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [ 'type' => 'string' ],
                        'description' => 'ID of the user to invite'
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Invitation sent',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Business rule violation',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['MISSING_USER_ID', 'CANNOT_INVITE_SELF', 'NOT_IN_A_ROOM', 'USER_ALREADY_IN_ROOM', 'ROOM_FULL', 'INVITATION_ALREADY_SENT']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'User not found',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['USER_NOT_FOUND']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Get(
            uriTemplate: '/invitation/sent',
            input: false,
            controller: MeListSentController::class,
            read: false,
            name: 'api_invitation_sent',
            openapiContext: [
                'summary' => 'List invitations sent by the current user',
                'description' => 'Returns invitations sent by the current user that are still pending (not accepted, denied, or revoked). Optionally filter by user_id as a query parameter.',
                'parameters' => [
                    [
                        'name' => 'user_id',
                        'in' => 'query',
                        'required' => false,
                        'schema' => [ 'type' => 'string' ],
                        'description' => 'Filter to invitations sent to this user (optional)'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'List of pending invitations sent by the user',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'invitations' => [
                                            'type' => 'array',
                                            'items' => [ 'type' => 'object' ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Get(
            uriTemplate: '/invitation/pending',
            input: false,
            controller: MeListPendingController::class,
            read: false,
            name: 'api_invitation_pending',
            openapiContext: [
                'summary' => 'List invitations received by the current user',
                'description' => 'Returns invitations received by the current user that are still pending (not accepted, denied, or revoked, and not expired).',
                'responses' => [
                    '200' => [
                        'description' => 'List of pending invitations received by the user',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'invitations' => [
                                            'type' => 'array',
                                            'items' => [ 'type' => 'object' ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/invitation/{id}/accept',
            input: false,
            controller: MeAcceptController::class,
            read: false,
            name: 'api_invitation_accept',
            openapiContext: [
                'summary' => 'Accept an invitation',
                'description' => 'Accepts an invitation, checks for expiration and revocation, and joins the new room.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [ 'type' => 'string' ],
                        'description' => 'ID of the invitation to accept'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Invitation accepted',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Business rule violation (expired, revoked, room deleted, etc.)',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['INVITATION_EXPIRED', 'INVITATION_REVOKED', 'ROOM_DELETED']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Invitation not found',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['INVITATION_NOT_FOUND']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/invitation/{id}/deny',
            input: false,
            controller: MeDenyController::class,
            read: false,
            name: 'api_invitation_deny',
            openapiContext: [
                'summary' => 'Deny an invitation',
                'description' => 'Denies an invitation by its id. Only the invited user can deny.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [ 'type' => 'string' ],
                        'description' => 'ID of the invitation to deny'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Invitation denied',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Invitation not found',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['INVITATION_NOT_FOUND']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/invitation/{id}/cancel',
            input: false,
            controller: MeCancelController::class,
            read: false,
            name: 'api_invitation_cancel',
            openapiContext: [
                'summary' => 'Cancel an invitation the user sent',
                'description' => 'Revokes an invitation by its id. Only the user who sent the invitation can revoke it.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [ 'type' => 'string' ],
                        'description' => 'ID of the invitation to cancel'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Invitation revoked',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Invitation not found or not allowed',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['INVITATION_NOT_FOUND']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
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
