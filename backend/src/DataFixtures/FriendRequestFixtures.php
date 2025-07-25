<?php

namespace App\DataFixtures;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FriendRequestFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    public function getEntityClass(): string
    {
        return FriendRequest::class;
    }

    public function getData(): iterable
    {
        yield [
            'dataSender' => 'User_1',
            'dataReceiver' => 'User_2',
        ];
    }

    protected function postInstantiate(object $entity, array $data): void
    {
        $sender = $this->getReference($data['dataSender'], User::class);
        $receiver = $this->getReference($data['dataReceiver'], User::class);

        $entity->setSender($sender);
        $entity->setReceiver($receiver);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
