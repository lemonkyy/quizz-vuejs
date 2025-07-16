<?php

namespace App\Controller\Api\Quizz;

use App\Message\GenerateQuizz;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class QuizzController extends AbstractController
{
    #[Route('/api/quizz', methods: ['POST'])]
    public function create(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $prompt = $content['prompt'] ?? '';

        if (empty($prompt)) {
            return $this->json(['error' => 'Prompt is required'], 400);
        }

        $bus->dispatch(new GenerateQuizz($prompt));

        return $this->json(['status' => 'processing']);
    }
}
