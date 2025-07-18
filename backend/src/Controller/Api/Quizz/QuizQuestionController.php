<?php

namespace App\Controller\Api\Quizz;

use App\Repository\QuizzQuestionsRepository;
use App\Repository\QuizzesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class QuizQuestionController extends AbstractController
{
    #[Route('/api/quizzes/{id}/questions', name: 'api_quizz_questions', methods: ['GET'])]
    public function getQuizQuestions(int $id, QuizzQuestionsRepository $repository, QuizzesRepository $quizzesRepository): JsonResponse
    {
        $quiz = $quizzesRepository->find($id);
        if (!$quiz) {
            return $this->json(['error' => 'Quiz not found'], 404);
        }

        $allQuestions = $repository->findBy(['quizz' => $id]);
        
        // Récupérer le count depuis le quiz, ou utiliser 10 par défaut pour les anciens quizzes
        $requestedCount = $quiz->getCount() ?? 10;
        
        // Si on a moins de questions que demandé, on prend toutes les questions
        if (count($allQuestions) <= $requestedCount) {
            $selectedQuestions = $allQuestions;
        } else {
            // Sélection aléatoire du nombre de questions demandé
            $randomKeys = array_rand($allQuestions, $requestedCount);
            if (!is_array($randomKeys)) {
                $randomKeys = [$randomKeys];
            }
            $selectedQuestions = array_map(function($key) use ($allQuestions) {
                return $allQuestions[$key];
            }, $randomKeys);
        }
        
        // Mélanger l'ordre des questions sélectionnées
        shuffle($selectedQuestions);

        $data = array_map(function ($question) {
            return [
                'id' => $question->getId(),
                'questionText' => $question->getQuestionText(),
                'options' => $question->getOptions(),
                'correctAnswer' => $question->getCorrectAnswer()
            ];
        }, $selectedQuestions);

        return $this->json($data);
    }
}
