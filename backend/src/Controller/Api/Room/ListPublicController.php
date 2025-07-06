<?php

namespace App\Controller\Api\Room;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//list public joinable rooms
class ListPublicController extends AbstractController
{
    private int $maxRoomUsers;

    public function __construct(ParameterBagInterface $params)
    {
        $this->maxRoomUsers = $params->get('app.max_room_users');
    }

    public function __invoke(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findPublicWithAvailableSlots($this->maxRoomUsers);
        return $this->json(['code' => 'SUCCESS', 'rooms' => $rooms], 200, [], ['groups' => ['room:read']]);
    }
}
