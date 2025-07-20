<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\QuizzesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: QuizzesRepository::class)]
#[ApiResource]
class Quizzes
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[ORM\Column(length: 100, nullable:true)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $contentJson = null;

    #[ORM\Column(nullable: true)]
    private ?int $createdBy = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $ready = false;

    #[ORM\Column(nullable: true)]
    private ?int $timePerQuestion = null;

    #[ORM\Column(nullable: true)]
    private ?int $count = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
    }

    public function getId(): ?UuidV7
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContentJson(): ?string
    {
        return $this->contentJson;
    }

    public function setContentJson(string $contentJson): self
    {
        $this->contentJson = $contentJson;

        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function isReady(): bool
    {
        return $this->ready;
    }

    public function setReady(bool $ready): self
    {
        $this->ready = $ready;
        return $this;
    }

    public function getTimePerQuestion(): ?int
    {
        return $this->timePerQuestion;
    }

    public function setTimePerQuestion(?int $timePerQuestion): self
    {
        $this->timePerQuestion = $timePerQuestion;
        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;
        return $this;
    }

}
