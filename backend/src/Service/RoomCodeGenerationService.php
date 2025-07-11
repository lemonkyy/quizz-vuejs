<?php

namespace App\Service;

use App\Repository\RoomRepository;

class RoomCodeGenerationService
{
    private RoomRepository $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function generateUniqueRoomCode(): string
    {
        $maxAttempts = 25;
        $attempts = 0;

        do {
            $code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            $attempts++;
            if ($attempts > $maxAttempts) {
                throw new \RuntimeException('Could not generate a unique room code.');
            }
        } while ($this->roomRepository->findOneBy(['code' => $code, 'deletedAt' => null]));

        return $code;
    }
}
