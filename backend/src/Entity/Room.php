<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Dto\Room\CreateDto;
use App\Api\Processor\Room\MeCreateProcessor;
use App\Api\Processor\Room\MeJoinProcessor;
use App\Api\Processor\Room\MeLeaveProcessor;
use App\Api\Processor\Room\MeDeleteProcessor;
use App\Api\Processor\Room\MeKickUserProcessor;
use App\Api\Processor\Room\SearchByCodeProcessor;
use App\Api\Provider\Room\ListPublicProvider;
use App\Api\Provider\Room\MeShowCurrentProvider;
use App\Repository\RoomRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(
    normalizationContext: ['groups' => ['room:read']],
    operations: [
        new Get(
            uriTemplate: '/room/current',
            provider: MeShowCurrentProvider::class,
            input: false,
            normalizationContext: ['groups' => ['room:read']],
            name: 'api_user_room',
            
        ),
        new GetCollection(
            uriTemplate: '/room/public',
            provider: ListPublicProvider::class,
            input: false,
            normalizationContext: ['groups' => ['room:read']],
            name: 'api_room_list_public',
        ),
        new Post(
            uriTemplate: '/room/{id}/kick',
            processor: MeKickUserProcessor::class,
            input: false,
            name: 'api_room_kick',
        ),
        new Post(
            uriTemplate: '/room/create',
            processor: MeCreateProcessor::class,
            input: CreateDto::class,
            normalizationContext: ['groups' => ['room:read']],
            name: 'api_room_create',
            
        ),
        new Delete(
            uriTemplate: '/room/delete',
            input: false,
            processor: MeDeleteProcessor::class,
            name: 'api_room_delete',
            
        ),
        new Post(
            uriTemplate: '/room/{id}/join',
            input: false,
            processor: MeJoinProcessor::class,
            name: 'api_room_join',
            
        ),
        new Post(
            uriTemplate: '/room/leave',
            input: false,
            processor: MeLeaveProcessor::class,
            name: 'api_room_leave',
            
        ),
        new Post(
            uriTemplate: '/room/search-by-code',
            input: false,
            processor: SearchByCodeProcessor::class,
            name: 'api_room_search_by_code',
        )
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
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt = null;

    #[Groups(['room:read'])]
    #[ORM\Column(type: 'boolean')]
    private bool $isPublic = false;

    #[Groups(['room:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[Groups(['room:read'])]
    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private ?string $code = null;

    #[Groups(['room:read'])]
    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomPlayer::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $roomPlayers;

    public function __construct()
    {
        $this->id = UuidV7::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->roomPlayers = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return Collection<int, RoomPlayer>|\RoomPlayer[]
     */
    public function getRoomPlayers(): Collection
    {
        return $this->roomPlayers;
    }

    public function addRoomPlayer(RoomPlayer $roomPlayer): self
    {
        if (!$this->roomPlayers->contains($roomPlayer)) {
            $this->roomPlayers->add($roomPlayer);
            $roomPlayer->setRoom($this);
        }

        return $this;
    }

    public function removeRoomPlayer(RoomPlayer $roomPlayer): self
    {
        if ($this->roomPlayers->removeElement($roomPlayer)) {
            // set the owning side to null (unless already changed)
            if ($roomPlayer->getRoom() === $this) {
                $roomPlayer->setRoom(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->code ?? '';
    }
}
