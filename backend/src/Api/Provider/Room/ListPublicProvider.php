<?php

namespace App\Api\Provider\Room;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\RoomRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Room;

class ListPublicProvider implements ProviderInterface
{
    private int $maxRoomUsers;

    public function __construct(ParameterBagInterface $params, private RoomRepository $roomRepository, private Pagination $pagination)
    {
        $this->maxRoomUsers = $params->get('app.max_room_users');
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Room::class && $operationName === 'api_room_list_public';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        [$page, , $limit] = $this->pagination->getPagination($operation, $context);
        return new Paginator($this->roomRepository->findPublicWithAvailableSlots($page, $limit));
    }
}
