<?php

namespace App\Message;

class GenerateQuizz
{
    public function __construct(
        public string $prompt
    ) {}
}
