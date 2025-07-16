<?php

namespace App\Entity;

use App\Repository\RoomPlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RoomPlayerRepository::class)]
class RoomPlayer
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[Groups(['room:read'])]
    #[ORM\OneToOne(inversedBy: 'roomPlayer', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $player = null;

    #[Groups(['room:read'])]
    #[ORM\Column()]
    private ?int $score = null;

    #[Groups(['room:read'])]
    #[ORM\ManyToOne(inversedBy: 'roomPlayers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Room $room = null;

    public function __construct(?User $player = null, ?Room $room = null, int $score = 0)
    {
        $this->id = UuidV7::v7();
        $this->player = $player;
        $this->room = $room;
        $this->score = $score;
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
