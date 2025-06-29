<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Controller\UserInfoController;
use App\Controller\RegisterController;
use App\Controller\UpdateUsernameController;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/user/info',
            controller: UserInfoController::class,
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
            controller: UpdateUsernameController::class,
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
        )
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private $username;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private string $password;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    public function getId(): ?int
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
}
