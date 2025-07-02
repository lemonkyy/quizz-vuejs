<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;
use App\Controller\Api\User\MeReadController;
use App\Controller\Api\User\MeUpdateUsernameController;
use App\Controller\Api\User\RegisterController;
use App\Controller\Api\User\MeGenerateTOTPSecret;
use App\Controller\Api\User\VerifyTOTPCode;
use App\Repository\UserRepository;
use SpecShaper\EncryptBundle\Annotations\Encrypted;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/user/info',
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
                                        'username' => ['type' => 'string'],
                                        'email' => ['type' => 'string'],
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
                                    'password' => ['type' => 'string'],
                                ],
                                'required' => ['email', 'password']
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '201' => [
                        'description' => 'User created.'
                    ],
                    '400' => [
                        'description' => 'Invalid input or email already in use.'
                    ]
                ]
            ]
        ),
        new Put(
            uriTemplate: '/user/username',
            input: false,
            controller: MeUpdateUsernameController::class,
            read: false,
            name: 'api_update_username',
            openapiContext: [
                'summary' => 'Update current user username',
                'description' => 'Updates the username of the current authenticated user.',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string'],
                                ],
                                'required' => ['username']
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Username updated.'
                    ],
                    '400' => [
                        'description' => 'Invalid username or already in use.'
                    ],
                    '401' => [
                        'description' => 'Unauthorized.'
                    ]
                ]
            ]
        ),
        new Get(
            uriTemplate: '/user/TOTP/secret',
            controller: MeGenerateTOTPSecret::class,
            read: false,
            name: 'api_user_totp_secret_generate',
            openapiContext: [
                'summary' => 'Generate TOTP secret for the current user',
                'description' => 'Generates a TOTP secret for the current authenticated user.',
                'responses' => [
                    '200' => [
                        'description' => 'TOTP secret generated successfully.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'TOTPSecret' => ['type' => 'string'],
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Failed to generate TOTP key.'
                    ],
                    '401' => [
                        'description' => 'Unauthorized'
                    ]
                ]
            ]
        ),
        new Post(
            uriTemplate: '/login-verify',
            controller: VerifyTOTPCode::class,
            read: false,
            name: 'api_user_totp_verify',
            openapiContext: [
                'summary' => 'Verify TOTP code for the current user',
                'description' => 'Verifies the TOTP code submitted by the user.',
                'responses' => [
                    '200' => ['description' => 'TOTP code verified and JWT returned.'],
                    '400' => ['description' => 'Invalid input.'],
                    '401' => ['description' => 'Unauthorized or invalid TOTP code.']
                ],
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'totp_code' => ['type' => 'string'],
                                    'temp_token' => ['type' => 'string']
                                ],
                                'required' => ['totp_code', 'temp_token']
                            ]
                        ]
                    ]
                ]
            ]
        )
    ]
)]

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(["room:read", "invitation:read"])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $email = null;

    #[Groups(["user:read", "room:read", "invitation:read"])]
    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[Encrypted]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $TOTPSecret = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
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
        $roles[] = "ROLE_USER";

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

    public function getTOTPSecret(): ?string
    {
        return $this->TOTPSecret;
    }

    public function setTOTPSecret(?string $TOTPSecret): self
    {
        $this->TOTPSecret = $TOTPSecret;
        return $this;
    }
}
