<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\Api\Room\ListPublicController;
use App\Controller\Api\Room\MeKickUserController;
use App\Controller\Api\Room\MeCreateController;
use App\Controller\Api\Room\MeDeleteController;
use App\Controller\Api\Room\MeShowCurrentController;
use App\Controller\Api\Room\MeJoinController;
use App\Controller\Api\Room\MeLeaveController;
use App\Repository\RoomRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/room/current',
            input: false,
            controller: MeShowCurrentController::class,
            read: false,
            name: 'api_user_room',
            openapiContext: [
                'summary' => 'Get the current room of the user',
                'description' => 'Show the room the user currently belongs to (code, users, owner, createdAt, isPublic).'
            ]
        ),
        new Post(
            uriTemplate: '/room/kick',
            controller: MeKickUserController::class,
            read: false,
            name: 'api_room_kick_user',
            openapiContext: [
                'summary' => 'Kick another user from the user\'s room',
                'description' => 'Kicks a user from the current room. Only the room owner can kick other users. Cannot kick yourself.',
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'user_id' => [
                                        'type' => 'string',
                                        'description' => 'ID of the user to kick from the room'
                                    ]
                                ],
                                'required' => ['user_id']
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'User kicked from room',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Business rule violation or invalid input',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '403' => [
                        'description' => 'Only the room owner can kick users',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
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
            uriTemplate: '/room/create',
            controller: MeCreateController::class,
            read: false,
            name: 'api_room_create',
            openapiContext: [
                'summary' => 'Create a new room',
                'description' => 'Creates a new room with the current user as owner and member. Optionally accepts a code and isPublic flag.',
                'requestBody' => [
                    'required' => false,
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'isPublic' => [
                                        'type' => 'boolean',
                                        'description' => 'Whether the room is public (optional)'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Room created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'string'],
                                        'code' => ['type' => 'string'],
                                        'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                        'isPublic' => ['type' => 'boolean'],
                                        'owner' => ['type' => 'string'],
                                        'users' => ['type' => 'array', 'items' => ['type' => 'string']]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Delete(
            uriTemplate: '/room/delete',
            input: false,
            controller: MeDeleteController::class,
            read: false,
            name: 'api_room_delete',
            openapiContext: [
                'summary' => 'Delete the room the user owns',
                'description' => 'Soft-deletes the room where the current user is the owner.',
                'responses' => [
                    '200' => [
                        'description' => 'Room deleted',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'No active room found for user as owner',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
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
            uriTemplate: '/room/{id}/join',
            input: false,
            controller: MeJoinController::class,
            read: false,
            name: 'api_room_join',
            openapiContext: [
                'summary' => 'Join a room by id',
                'description' => 'Current user joins a room by its id.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [ 'type' => 'string' ],
                        'description' => 'ID of the room to join'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Joined room',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'string'],
                                        'code' => ['type' => 'string'],
                                        'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                        'isPublic' => ['type' => 'boolean'],
                                        'owner' => ['type' => 'string'],
                                        'users' => ['type' => 'array', 'items' => ['type' => 'string']]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Business rule violation or invalid input',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Room not found',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
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
            uriTemplate: '/room/leave',
            input: false,
            controller: MeLeaveController::class,
            read: false,
            name: 'api_room_leave',
            openapiContext: [
                'summary' => 'Leave the current room',
                'description' => 'Current user leaves their active room.',
                'responses' => [
                    '200' => [
                        'description' => 'Left room successfully',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'User is not in an active room',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
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
            uriTemplate: '/room/public',
            input: false,
            controller: ListPublicController::class,
            read: false,
            name: 'api_room_list_public',
            openapiContext: [
                'summary' => 'Get all public rooms',
                'description' => 'Returns a list of all public rooms, including their ID, owner, users, creation date, and public status.',
                'responses' => [
                    '200' => [
                        'description' => 'A list of public rooms',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'string'],
                                            'owner' => ['type' => 'string'],
                                            'users' => ['type' => 'array', 'items' => ['type' => 'string']],
                                            'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                            'isPublic' => ['type' => 'boolean'],
                                        ]
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

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[Groups(['room:read', 'invitation:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[Groups(['room:read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[Groups(['room:read'])]
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'room_users')]
    private Collection $users;

    #[Groups(['room:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt = null;

    #[Groups(['room:read'])]
    #[ORM\Column(type: 'boolean')]
    private bool $isPublic = false;

    #[Groups(['room:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
        $this->users = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
