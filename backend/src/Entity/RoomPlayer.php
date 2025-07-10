<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RoomPlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RoomPlayerRepository::class)]
#[ApiResource]
class RoomPlayer
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[ORM\OneToOne(mappedBy: 'roomPlayer', targetEntity: User::class)]
    private ?User $player = null;

    #[ORM\Column()]
    private ?int $score = null;

    #[ORM\ManyToOne(inversedBy: 'roomPlayers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Room $room = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }
}
