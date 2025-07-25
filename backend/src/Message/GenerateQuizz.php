<?php

namespace App\Message;

class GenerateQuizz
{
    public function __construct(
        public string $prompt,
        public int $count,
        public int $timePerQuestion,
        public string $userId
    ) {}
}
