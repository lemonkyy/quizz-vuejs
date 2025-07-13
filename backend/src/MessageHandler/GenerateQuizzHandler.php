<?php

namespace App\MessageHandler;

use App\Message\GenerateQuizz;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Quizzes;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ParseQuizService;

/* #[AsMessageHandler]
class GenerateQuizzHandler
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private ParseQuizService $parseQuizService
    ) {}

    public function __invoke(GenerateQuizz $message): void
    {
        $prompt = $message->prompt;
        $count = $message->count ?? 10;

        $this->logger->info("Start handling prompt: {$prompt} with count: {$count}");
        file_put_contents('/tmp/quizz_generated.log', "START handling prompt: $prompt with count: $count\n", FILE_APPEND);

        $response = $this->client->request('POST', 'http://ollama:11434/api/generate', [
            'json' => [
                'model' => 'mistral',
                'prompt' => "Ignore le contexte précédent. Génére-moi un quizz au format JSON sur le thème : \"$prompt\" avec exactement $count questions. Vérifie que les réponses soit factuellement correctes.
                Le JSON doit avoir :
                - \"questions\": tableau contenant
                    - \"question\": string
                    - \"options\": tableau de strings
                    - \"correct_answer\": string
                Exemple attendu :
                {
                    \"questions\": [
                        {
                            \"question\": \"Qui a peint la Joconde ?\",
                            \"options\": [\"Michel-Ange\", \"Raphaël\", \"Léonard de Vinci\", \"Van Gogh\"],
                            \"correct_answer\": \"Léonard de Vinci\"
                        }
                    ]
                }
                Pas d'autres texte que le JSON.",
                'stream' => true,
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $this->logger->info("Request sent to Ollama");
        file_put_contents('/tmp/quizz_generated.log', "REQUEST SENT\n", FILE_APPEND);

        $parsedContent = $this->parseStreamResponse($response);

        $this->logger->info("Parsing complete, content length: " . strlen($parsedContent));
        file_put_contents('/tmp/quizz_generated.log',
            "---- FINAL PARSED ----\n" . $parsedContent . "\n",
            FILE_APPEND
        );

        try {
            $quiz = new Quizzes();
            $quiz->setContentJson($parsedContent);
            $quiz->setTitle($prompt);
            $this->entityManager->persist($quiz);
            file_put_contents('/tmp/quizz_generated.log', "persisted OK\n", FILE_APPEND);

            $this->entityManager->flush();
            file_put_contents('/tmp/quizz_generated.log', "flushed OK\n", FILE_APPEND);

            $this->entityManager->clear();
            $quizId = $quiz->getId();
            $quiz = $this->entityManager->getRepository(Quizzes::class)->find($quizId);

            $this->parseQuizService->parseAndPersistQuestions($quiz->getId());
            file_put_contents('/tmp/quizz_generated.log', "parseAndPersistQuestions DONE\n", FILE_APPEND);

            $conn = $this->entityManager->getConnection()->getDatabase();
            file_put_contents('/tmp/quizz_generated.log', "using DB: $conn\n", FILE_APPEND);

            $id = $quiz->getId();
            file_put_contents('/tmp/quizz_generated.log', "QUIZ PERSISTED WITH ID: " . $id . "\n", FILE_APPEND);
        } catch (\Throwable $e) {
            $this->logger->error("Error while persisting quiz: ".$e->getMessage());
            file_put_contents('/tmp/quizz_generated.log', "ERROR PERSISTING QUIZ: ".$e->getMessage(), FILE_APPEND);
            throw $e;
        }
    }

    private function parseStreamResponse($response): string {
        $buffer = '';
        foreach ($this->client->stream($response) as $chunk) {
            $content = $chunk->getContent();
            file_put_contents('/tmp/quizz_generated.log',
                "==== NEW CHUNK ====\n" . $content . "\n",
                FILE_APPEND
            );

            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $data = json_decode($line, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    file_put_contents('/tmp/quizz_generated.log',
                        "!!! JSON decode error: " . json_last_error_msg() . " for line: [$line]\n",
                        FILE_APPEND
                    );
                    continue;
                }
                if (is_array($data) && array_key_exists('response', $data)) {
                    $buffer .= $data['response'];
                    file_put_contents('/tmp/quizz_generated.log',
                        "+++ ADDED TO BUFFER: " . $data['response'] . "\n" .
                        "=== CURRENT BUFFER: " . $buffer . "\n",
                        FILE_APPEND
                    );
                } else {
                    file_put_contents('/tmp/quizz_generated.log',
                        "!!! JSON valid but no response field: " . print_r($data, true) . "\n",
                        FILE_APPEND
                    );
                }
            }
        }
        file_put_contents('/tmp/quizz_generated.log',
            "==== FINAL BUFFER ====\n" . $buffer . "\n",
            FILE_APPEND
        );
        return $buffer;
    }
} */

#[AsMessageHandler]
class GenerateQuizzHandler
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private ParseQuizService $parseQuizService
    ) {}

    public function __invoke(GenerateQuizz $message): void
    {
        $prompt = $message->prompt;
        $count = $message->count ?? 10;

        $this->logger->info("Start handling prompt: {$prompt} with count: {$count}");
        file_put_contents('/tmp/quizz_generated.log', "START handling prompt: $prompt with count: $count\n", FILE_APPEND);

        $response = $this->client->request('POST', 'http://ollama:11434/api/generate', [
            'json' => [
                'model' => 'llama3',
                'prompt' => "Ignore le contexte précédent. Génére-moi un quizz au format JSON sur le thème : \"$prompt\" avec exactement $count questions.
                Le JSON doit avoir :
                - \"questions\": tableau contenant
                    - \"question\": string
                    - \"options\": tableau de strings
                    - \"correct_answer\": string
                Pas d'autres texte que le JSON.",
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        file_put_contents('/tmp/quizz_generated.log', "---- FULL RESPONSE ----\n" . $content . "\n", FILE_APPEND);

        try {
            $quiz = new Quizzes();
            $quiz->setContentJson($content);
            $quiz->setTitle($prompt);
            $this->entityManager->persist($quiz);
            file_put_contents('/tmp/quizz_generated.log', "persisted OK\n", FILE_APPEND);

            $this->entityManager->flush();
            file_put_contents('/tmp/quizz_generated.log', "flushed OK\n", FILE_APPEND);

            $this->parseQuizService->parseAndPersistQuestions($quiz->getId());
            file_put_contents('/tmp/quizz_generated.log', "parseAndPersistQuestions DONE\n", FILE_APPEND);

            $conn = $this->entityManager->getConnection()->getDatabase();
            file_put_contents('/tmp/quizz_generated.log', "using DB: $conn\n", FILE_APPEND);

            $id = $quiz->getId();
            file_put_contents('/tmp/quizz_generated.log', "QUIZ PERSISTED WITH ID: " . $id . "\n", FILE_APPEND);
        } catch (\Throwable $e) {
            $this->logger->error("Error while persisting quiz: ".$e->getMessage());
            file_put_contents('/tmp/quizz_generated.log', "ERROR PERSISTING QUIZ: ".$e->getMessage(), FILE_APPEND);
            throw $e;
        }
    }
}