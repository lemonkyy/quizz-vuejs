<?php

namespace App\Service;

class SwearWordChecker
{
    private array $swearWords;

    public function __construct()
    {
        $this->swearWords = [
            "con", "connard", "connasse", "conne", "enculé", "encule", "enculee", "fdp", "fils de pute", "pute", "salope", "salopard", "merde", "merdique", "chiant", "chieuse", "chieur", "niquer", "nique", "nq", "ntm", "ta mère", "ta mere", "tg", "batard", "bâtard", "batarde", "bâtarde", "bouffon", "bouffonne", "pd", "pédé", "pede", "tapette", "gouine", "encu", "zgeg", "teubé", "abruti", "débile", "mongol", "mongole", "attardé", "attardee", "putain", "put1", "ptn", "bordel", "cul", "trou du cul", "tarlouze", "grognasse", "baltringue", "clochard", "cassos", "bougnoule", "négro", "negro", "youpin", "clocharde"
        ];
    }

    public function containsSwearWord(string $text): bool
    {
        $textLower = mb_strtolower($text);
        foreach ($this->swearWords as $word) {
            if (str_contains($textLower, $word)) {
                return true;
            }
        }
        return false;
    }

    public function getFoundSwearWords(string $text): array
    {
        $found = [];
        $textLower = mb_strtolower($text);
        foreach ($this->swearWords as $word) {
            if (str_contains($textLower, $word)) {
                $found[] = $word;
            }
        }
        return $found;
    }
}
