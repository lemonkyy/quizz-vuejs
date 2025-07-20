<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\QuizzQuestionsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: QuizzQuestionsRepository::class)]
#[ApiResource]
class QuizzQuestions
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidV7 $id = null;

    #[ORM\ManyToOne(targetEntity: Quizzes::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quizzes $quizz = null;

    #[ORM\Column(type: "text")]
    private ?string $questionText = null;

    #[ORM\Column(type: "text")]
    private ?string $correctAnswer = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $options = null;

    public function __construct()
    {
        $this->id = UuidV7::v7();
    }

    public function getId(): ?UuidV7 { return $this->id; }

    public function getQuizz(): ?Quizzes { return $this->quizz; }

    public function setQuizz(?Quizzes $quizz): self { $this->quizz = $quizz; return $this; }

    public function getQuestionText(): ?string { return $this->questionText; }

    public function setQuestionText(string $questionText): self { $this->questionText = $questionText; return $this; }

    public function getCorrectAnswer(): ?string { return $this->correctAnswer; }

    public function setCorrectAnswer(string $correctAnswer): self { $this->correctAnswer = $correctAnswer; return $this; }

    public function getOptions(): ?array { return $this->options; }

    public function setOptions(?array $options): self { $this->options = $options; return $this; }
}
