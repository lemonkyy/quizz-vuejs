<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RoomFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    public function getEntityClass(): string
    {
        return Room::class;
    }

    public function getData(): iterable
    {
        yield [
            'dataOwner' => 'User_1',
            'isPublic' => true,
            'code' => 'room1',
        ];

        yield [
            'dataOwner' => 'User_4',
            'isPublic' => false,
            'code' => 'room2',
        ];
    }

    protected function postInstantiate(object $entity, array $data): void
    {
        $owner = $this->getReference($data['dataOwner'], User::class);
        $entity->setOwner($owner);
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
