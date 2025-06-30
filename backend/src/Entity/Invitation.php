<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV7;

#[ApiResource]
#[ORM\Entity]
class Invitation
{
    #[Groups(['invitation:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $group;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $invitedBy;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $invitedUser;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private $invitedAt;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $acceptedAt;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $revokedAt;

    #[Groups(['invitation:read'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $deniedAt;

    public function __construct()
    {
        $this->invitedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;
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
