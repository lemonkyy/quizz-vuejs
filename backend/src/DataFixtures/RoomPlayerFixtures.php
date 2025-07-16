<?php

namespace App\DataFixtures;

use App\Entity\RoomPlayer;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RoomPlayerFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    public function getEntityClass(): string
    {
        return RoomPlayer::class;
    }

    public function getData(): iterable
    {
        yield [
            'dataPlayer' => 'User_1',
            'dataRoom' => 'Room_1',
            'score' => 0,
        ];

        yield [
            'dataPlayer' => 'User_2',
            'dataRoom' => 'Room_1',
            'score' => 0,
        ];

        yield [
            'dataPlayer' => 'User_3',
            'dataRoom' => 'Room_1',
            'score' => 0,
        ];

        yield [
            'dataPlayer' => 'User_4',
            'dataRoom' => 'Room_2',
            'score' => 0,
        ];

        yield [
            'dataPlayer' => 'User_5',
            'dataRoom' => 'Room_2',
            'score' => 0,
        ];
    }

    protected function postInstantiate(object $entity, array $data): void
    {
        $player = $this->getReference($data['dataPlayer'], User::class);
        $room = $this->getReference($data['dataRoom'], Room::class);

        $entity->setPlayer($player);
        $entity->setRoom($room);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            RoomFixtures::class,
        ];
    }
}
