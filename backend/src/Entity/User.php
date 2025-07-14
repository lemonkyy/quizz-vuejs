<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Api\Dto\User\GetByUsernameDto;
use App\Api\Dto\User\RegisterDto;
use App\Api\Dto\User\SearchDto;
use App\Api\Processor\User\MeGenerateTotpSecretProcessor;
use App\Api\Processor\User\MeUpdateProcessor;
use App\Api\Processor\User\RegisterProcessor;
use App\Api\Processor\User\RemoveFriendProcessor;
use App\Api\Processor\User\VerifyTotpCodeProcessor;
use App\Api\Provider\User\GetByUsernameProvider;
use App\Api\Provider\User\MeListFriendsProvider;
use App\Api\Provider\User\MeReadProvider;
use App\Api\Provider\User\SearchProvider;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SpecShaper\EncryptBundle\Annotations\Encrypted;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

use App\Api\Dto\User\UpdateDto;
use App\Api\Dto\User\VerifyTotpCodeDto;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/user/get-by-username',
            provider: GetByUsernameProvider::class,
            input: GetByUsernameDto::class,
            name: 'api_user_get_by_username',  
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Get(
            uriTemplate: '/user/me',
            provider: MeReadProvider::class,
            name: 'api_user_info',
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Get(
            uriTemplate: '/user/search',
            provider: SearchProvider::class,
            input: SearchDto::class,
            name: 'api_user_search',
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Post(
            uriTemplate: '/register',
            processor: RegisterProcessor::class,
            input: RegisterDto::class,
            name: 'api_user_register',
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Put(
            uriTemplate: '/user',
            processor: MeUpdateProcessor::class,
            input: UpdateDto::class,
            name: 'api_user_update',
        ),
        new Post(
            uriTemplate: '/user/totp/secret',
            processor: MeGenerateTotpSecretProcessor::class,
            input: false,
            name: 'api_user_totp_secret_generate',
        ),
        new Post(
            uriTemplate: '/login-verify',
            processor: VerifyTotpCodeProcessor::class,
            input: VerifyTotpCodeDto::class,
            name: 'api_user_totp_verify',
        ),
        new Delete(
            uriTemplate: '/user/friends/{id}',
            processor: RemoveFriendProcessor::class,
            input: false,
            name: 'api_user_remove_friend',
        ),
        new Get(
            uriTemplate: '/user/friends',
            provider: MeListFriendsProvider::class,
            input: false,
            normalizationContext: ['groups' => ['user:read']],
            name: 'api_user_list_friends',
        ),   
    ]
)]

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['room:read', 'invitation:read', 'friendRequest:read', 'user:read'])]
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

    #[Groups(['user:read'])]
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

    #[ORM\OneToMany(mappedBy: 'invitedBy', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $sentInvitations;

    #[ORM\OneToMany(mappedBy: 'invitedUser', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $receivedInvitations;

    public function __construct()
    {
        $this->id = UuidV7::v7();
        $this->sentFriendRequests = new ArrayCollection();
        $this->receivedFriendRequests = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->sentInvitations = new ArrayCollection();
        $this->receivedInvitations = new ArrayCollection();
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

    /**
     * @return Collection<int, Invitation>|Invitation[]
     */
    public function getSentInvitations(): Collection
    {
        return $this->sentInvitations;
    }

    /**
     * @return Collection<int, Invitation>|Invitation[]
     */
    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }
}
