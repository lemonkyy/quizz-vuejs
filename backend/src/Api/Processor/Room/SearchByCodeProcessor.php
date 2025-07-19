<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Exception\ValidationException;

class SearchByCodeProcessor implements ProcessorInterface
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $request = $context['request'] ?? null;
        $requestData = [];
        
        if ($request) {
            $content = $request->getContent();
            if ($content) {
                $requestData = json_decode($content, true) ?? [];
            }
        }
        
        $code = $requestData['code'] ?? null;
        
        if (!$code) {
            throw new ValidationException('ERR_CODE_REQUIRED', 'Room code is required', 400);
        }
        
        $room = $this->roomRepository->findByCode($code);
        
        if (!$room) {
            throw new ValidationException('ERR_ROOM_NOT_FOUND', 'Room not found with this code', 404);
        }
        
        if ($room->getDeletedAt() !== null) {
            throw new ValidationException('ERR_ROOM_DELETED', 'Room has been deleted', 400);
        }
        
        return [
            'id' => $room->getId(),
            'code' => $room->getCode(),
            'isPublic' => $room->isPublic(),
            'createdAt' => $room->getCreatedAt()->format('c')
        ];
    }
}