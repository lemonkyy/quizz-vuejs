<?php

namespace App\Entity;

use App\Repository\ProfilePictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: ProfilePictureRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/profile-pictures', controller: \App\Controller\Api\ProfilePicture\ListController::class),
    ],
    normalizationContext: ['groups' => ['profilePicture:read']]
)]
class ProfilePicture
{
    #[Groups(['user:read', 'room:read', 'user:read', 'invitation:read', 'friendRequest:read', 'profilePicture:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[Groups(['user:read', 'profilePicture:read'])]
    #[ORM\Column(length: 255, type: "string", unique: true)]
    private ?string $fileName = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }
}
