<?php

namespace App\Service;

use App\Entity\QuizzQuestions;
use App\Repository\QuizzesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\UuidV7;


class ParseQuizService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private QuizzesRepository $quizzesRepository,
        private LoggerInterface $logger,
    ) {}

    public function parseAndPersistQuestions(UuidV7 $quizId, ?int $expectedCount = null): void
    {
        $quiz = $this->quizzesRepository->find($quizId);

        if (!$quiz) {
            throw new \Exception("Quiz with ID $quizId not found.");
        }

        $content = trim($quiz->getContentJson());
        $questionsCount = 0;

        if (str_starts_with($content, '{')) {
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON: " . json_last_error_msg());
            }

            if ($expectedCount && count($data['questions']) !== $expectedCount) {
                $this->logger->warning("Expected $expectedCount questions but got " . count($data['questions']) . " questions for quiz ID $quizId");
            }

            foreach ($data['questions'] as $q) {
                $question = new QuizzQuestions();
                $question->setQuizz($quiz);
                $question->setQuestionText($q['question']);
                $question->setCorrectAnswer($q['correct_answer']);
                $question->setOptions($q['options']);
                $this->entityManager->persist($question);
                $this->logger->info("Added question from JSON: " . $q['question']);
                $questionsCount++;
            }
        } else {
            preg_match('/Réponses\s*:\s*(.*)/i', $content, $answerMatch);
            $answerMap = [];

            if (isset($answerMatch[1])) {
                preg_match_all('/(\d+)-([a-d])/', $answerMatch[1], $answerPairs, PREG_SET_ORDER);
                foreach ($answerPairs as $pair) {
                    $answerMap[(int)$pair[1]] = $pair[2];
                }
            }

            preg_match_all('/(\d+)\.\s(.*?)\n\s*a\)(.*?)\n\s*b\)(.*?)\n\s*c\)(.*?)\n\s*d\)(.*?)\n/', $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $questionNumber = (int)$match[1];
                $questionText = trim($match[2]);
                $options = [
                    'a' => trim($match[3]),
                    'b' => trim($match[4]),
                    'c' => trim($match[5]),
                    'd' => trim($match[6]),
                ];
                $correctLetter = $answerMap[$questionNumber] ?? null;
                $correctAnswer = $correctLetter ? $options[$correctLetter] : null;

                $question = new QuizzQuestions();
                $question->setQuizz($quiz);
                $question->setQuestionText($questionText);
                $question->setCorrectAnswer($correctAnswer ?? "TODO");
                //$question->setOptions($q['options']);
                $question->setOptions($options);

                $this->entityManager->persist($question);
                $this->logger->info("Added question: " . $questionText . " => Answer: " . ($correctAnswer ?? "TODO"));
                $questionsCount++;
            }

            if ($expectedCount && $questionsCount !== $expectedCount) {
                $this->logger->warning("Expected $expectedCount questions but got $questionsCount questions for quiz ID $quizId");
            }
        }

        $quiz->setReady(true);
        $this->entityManager->flush();

        $this->logger->info("Quiz ID $quizId marked as ready.");
    }
}
