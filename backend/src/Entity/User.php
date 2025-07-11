<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;
use App\Controller\Api\User\MeReadController;
use App\Controller\Api\User\MeUpdateController;
use App\Controller\Api\User\RegisterController;
use App\Controller\Api\User\MeGenerateTotpSecretController;
use App\Controller\Api\User\MeListFriendsController;
use App\Controller\Api\User\RemoveFriendController;
use App\Controller\Api\User\VerifyTotpCodeController;
use App\Repository\UserRepository;
use SpecShaper\EncryptBundle\Annotations\Encrypted;
use App\Controller\Api\User\SearchController;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/user',
            input: false,
            controller: MeReadController::class,
            read: false,
            name: 'api_user_info',
            openapiContext: [
                'summary' => 'Get current user info',
                'description' => 'Returns the username and email of the current authenticated user.',
                'responses' => [
                    '200' => [
                        'description' => 'User info',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'user' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'username' => ['type' => 'string'],
                                                'email' => ['type' => 'string'],
                                                'profilePictureURL' => ['type' => 'string'],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized'
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/user/search',
            controller: SearchController::class,
            read: false,
            name: 'api_user_search',
            openapiContext: [
                'summary' => 'Search for users by username',
                'description' => 'Returns a list of users matching the provided username, with pagination.',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string', 'description' => 'The username to search for', 'example' => 'testuser'],
                                    'page' => ['type' => 'integer', 'default' => 1, 'description' => 'The page number for pagination'],
                                    'limit' => ['type' => 'integer', 'default' => 10, 'description' => 'The number of results per page']
                                ],
                                'required' => ['username']
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'List of users',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'users' => ['type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => ['type' => 'string'],
                                                    'username' => ['type' => 'string'],
                                                    'profilePictureUrl' => ['type' => 'string']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized'
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/register',
            controller: RegisterController::class,
            read: false,
            name: 'api_register',
            openapiContext: [
                'summary' => 'Register a new user',
                'description' => 'Registers a new user with email and password.',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string'],
                                    'username' => ['type' => 'string'],
                                    'password' => ['type' => 'string'],
                                    'passwordConfirmation' => ['type' => 'string'],
                                    'tosAgreedTo' => ['type' => 'boolean']
                                ],
                                'required' => ['email', 'password', 'tosAgreedTo']
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'User created.',
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
                        'description' => 'Invalid input or email already in use.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['MISSING_CREDENTIALS', 'INVALID_EMAIL', 'EMAIL_ALREADY_IN_USE', 'USERNAME_VALIDATION_FAILED', 'USERNAME_GENERATION_FAILED', 'ERR_PASSWORD_WEAK', 'ERR_TOS_REFUSED']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Put(
            uriTemplate: '/user',
            input: false,
            controller: MeUpdateController::class,
            read: false,
            name: 'api_update_user',
            openapiContext: [
                'summary' => 'Update current user',
                'description' => 'Updates the authenticated user and refreshes his cookies.',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'newUsername' => ['type' => 'string'],
                                    'newProfilePictureId' => ['type' => 'string'],
                                    'clearTotpSecret' => ['type' => 'boolean']
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Username updated.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'message' => ['type' => 'string'],
                                        'username' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Invalid username or already in use.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['INVALID_USERNAME', 'ERR_USERNAME_INVALID_TYPE', 'ERR_USERNAME_CONTAINS_SPACES', 'ERR_USERNAME_LENGTH', 'ERR_USERNAME_INAPPROPRIATE', 'ERR_USERNAME_TAKEN', 'ERR_NULL_PROFILE_PICTURE', 'ERR_PROFILE_PICTURE_NOT_FOUND']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized.'
                    ]
                ]
            ]
        ),
        new Get(
            uriTemplate: '/user/totp/secret',
            controller: MeGenerateTotpSecretController::class,
            read: false,
            name: 'api_user_totp_secret_generate',
            openapiContext: [
                'summary' => 'Generate TOTP secret for the current user and refreshes his cookies',
                'description' => 'Generates a TOTP secret for the current authenticated user.',
                'responses' => [
                    '200' => [
                        'description' => 'TOTP secret generated successfully.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'totpSecret' => ['type' => 'string'],
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Failed to generate TOTP key.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['TOTP_GENERATION_FAILED']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized'
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/login-verify',
            controller: VerifyTotpCodeController::class,
            read: false,
            name: 'api_user_totp_verify',
            openapiContext: [
                'summary' => 'Verify TOTP code for the current user',
                'description' => 'Verifies the TOTP code submitted by the user.',
                'responses' => [
                    '200' => [
                        'description' => 'TOTP code verified and JWT returned.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['SUCCESS']],
                                        'token' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Invalid input.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['MISSING_TOTP_CREDENTIALS']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized or invalid TOTP code.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'string', 'enum' => ['INVALID_TEMP_TOKEN', 'INVALID_TOTP_CODE']],
                                        'error' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'totpCode' => ['type' => 'string'],
                                    'tempToken' => ['type' => 'string']
                                ],
                                'required' => ['totpCode', 'tempToken']
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Delete(
            uriTemplate: '/user/friends/{id}',
            controller: RemoveFriendController::class,
            read: false,
            name: 'api_user_remove_friend',
            openapiContext: [
                'summary' => 'Remove a friend',
                'description' => 'Removes a friend from the current user\'s friend list.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                        'description' => 'The ID of the friend to remove.'
                    ]
                ],
                'responses' => [
                    '200' => ['description' => 'Friend removed successfully'],
                    '400' => ['description' => 'Invalid request'],
                    '404' => ['description' => 'User not found or not a friend'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        new Get(
            uriTemplate: '/user/friends',
            controller: MeListFriendsController::class,
            read: false,
            name: 'api_user_list_friends',
            openapiContext: [
                'summary' => 'List current user\'s friends',
                'description' => 'Lists all friends of the current authenticated user.',
                'responses' => [
                    '200' => ['description' => 'List of friends'],
                    '401' => ['description' => 'Unauthorized']
                ]
            ]
        ),
        
    ]
)]

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['room:read', 'invitation:read', 'friendRequest:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $email = null;

    #[Groups(['user:read', 'room:read', 'invitation:read', 'friendRequest:read'])]
    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[Encrypted]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totpSecret = null;

    #[ORM\OneToOne(mappedBy: 'player', targetEntity: RoomPlayer::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?RoomPlayer $roomPlayer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProfilePicture $profilePicture = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: FriendRequest::class, orphanRemoval: true)]
    private Collection $sentFriendRequests;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: FriendRequest::class, orphanRemoval: true)]
    private Collection $receivedFriendRequests;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'friends')]
    #[ORM\JoinTable(name: 'user_friends')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'friend_user_id', referencedColumnName: 'id')]
    private Collection $friends;

    public function __construct()
    {
        $this->id = UuidV7::v7();
        $this->sentFriendRequests = new ArrayCollection();
        $this->receivedFriendRequests = new ArrayCollection();
        $this->friends = new ArrayCollection();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): self
    {
        $this->totpSecret = $totpSecret;
        return $this;
    }

    public function getRoomPlayer(): ?RoomPlayer
    {
        return $this->roomPlayer;
    }

    public function setRoomPlayer(?RoomPlayer $roomPlayer)
    {
        $this->roomPlayer = $roomPlayer;

        return $this;
    }

    public function getProfilePicture(): ?ProfilePicture
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?ProfilePicture $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    #[Groups(['user:read', 'room:read', 'user:read', 'invitation:read', 'friendRequest:read'])]
    public function getProfilePictureUrl(): ?string
    {
        if (!$this->profilePicture) {
            return null;
        }

        return '/uploads/profile_pictures/' . $this->profilePicture->getFileName();
    }

    /**
     * @return Collection<int, FriendRequest>|\App\Entity\FriendRequest[]
     */
    public function getSentFriendRequests(): Collection
    {
        return $this->sentFriendRequests;
    }

    public function addSentFriendRequest(FriendRequest $sentFriendRequest): static
    {
        if (!$this->sentFriendRequests->contains($sentFriendRequest)) {
            $this->sentFriendRequests->add($sentFriendRequest);
            $sentFriendRequest->setSender($this);
        }

        return $this;
    }

    public function removeSentFriendRequest(FriendRequest $sentFriendRequest): static
    {
        if ($this->sentFriendRequests->removeElement($sentFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($sentFriendRequest->getSender() === $this) {
                $sentFriendRequest->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>|\App\Entity\FriendRequest[]
     */
    public function getReceivedFriendRequests(): Collection
    {
        return $this->receivedFriendRequests;
    }

    public function addReceivedFriendRequest(FriendRequest $receivedFriendRequest): static
    {
        if (!$this->receivedFriendRequests->contains($receivedFriendRequest)) {
            $this->receivedFriendRequests->add($receivedFriendRequest);
            $receivedFriendRequest->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedFriendRequest(FriendRequest $receivedFriendRequest): static
    {
        if ($this->receivedFriendRequests->removeElement($receivedFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($receivedFriendRequest->getReceiver() === $this) {
                $receivedFriendRequest->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(User $friend): static
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
        }

        return $this;
    }

    public function removeFriend(User $friend): static
    {
        $this->friends->removeElement($friend);

        return $this;
    }
}
