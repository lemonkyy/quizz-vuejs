<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Service\RoomMembershipService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Room;
use App\Entity\User;
use App\Api\Dto\Room\JoinDto;
use App\Exception\ValidationException;

class MeJoinProcessor implements ProcessorInterface
{
    private int $maxRoomUsers;

    public function __construct(private RoomRepository $roomRepository, private RoomMembershipService $roomMembershipService, private Security $security, ParameterBagInterface $params)
    {
        $this->maxRoomUsers = $params->get('app.max_room_users');
    }

    /**
     * @param JoinDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Room
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        
        $roomId = $uriVariables['id'];

        $room = $this->roomRepository->findOneBy(['id' => $roomId]);

        if (!$room) {
            throw new ValidationException('ERR_ROOM_NOT_FOUND', 'Room not found', 404);
        }

        if ($room->getDeletedAt() !== null) {
            throw new ValidationException('ERR_ROOM_DELETED', 'Room has been deleted', 400);
        }

        if ($room->getRoomPlayers()->contains($user->getRoomPlayer())) {
            throw new ValidationException('ERR_USER_ALREADY_IN_ROOM', 'User is already in this room', 400);
        }

        if (count($room->getRoomPlayers()) >= $this->maxRoomUsers) {
            throw new ValidationException('ERR_ROOM_FULL', 'Room is at max capacity', 400);
        }

        $this->roomMembershipService->handleUserJoiningRoom($user, $room);

        return $room;
    }
}
