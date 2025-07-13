<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;
use App\Entity\User;
use App\Service\RoomMembershipService;
use App\Api\Dto\Room\CreateDto;
use Symfony\Component\Serializer\SerializerInterface;

class MeCreateProcessor implements ProcessorInterface
{
    public function __construct(private RoomMembershipService $roomMembershipService, private SerializerInterface $serializer)
    {
    }

    /**
     * @param CreateDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $isPublic = $data->isPublic;

        $room = $this->roomMembershipService->handleUserCreatingRoom($user, $isPublic);

        $serializedRoom = $this->serializer->serialize($room, 'json', ['groups' => ['room:read']]);
        $roomData = json_decode($serializedRoom, true);

        return new JsonResponse(['code' => 'SUCCESS', 'room' => $roomData], 201);
    }
}
