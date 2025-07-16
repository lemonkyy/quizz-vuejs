<?php

namespace App\Controller\Api\Quizz;

use App\Repository\QuizzQuestionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class QuizQuestionController extends AbstractController
{
    #[Route('/api/quizzes/{id}/questions', name: 'api_quizz_questions', methods: ['GET'])]
    public function getQuizQuestions(int $id, QuizzQuestionsRepository $repository): JsonResponse
    {
        $questions = $repository->findBy(['quizz' => $id]);

        $data = array_map(function ($question) {
            return [
                'id' => $question->getId(),
                'questionText' => $question->getQuestionText(),
                'options' => $question->getOptions(),
                'correctAnswer' => $question->getCorrectAnswer()
            ];
        }, $questions);

        return $this->json($data);
    }
}
