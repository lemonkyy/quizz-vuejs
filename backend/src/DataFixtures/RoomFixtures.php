<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RoomFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user1 = $manager->getRepository(User::class)->findOneBy(['username' => 'user1']);
        $user2 = $manager->getRepository(User::class)->findOneBy(['username' => 'user2']);
        $user3 = $manager->getRepository(User::class)->findOneBy(['username' => 'user3']);

        $room1 = new Room();
        $room1->setOwner($user1);
        $room1->addUser($user1);
        $room1->addUser($user2);
        $room1->addUser($user3);
        $room1->setIsPublic(true);
        $manager->persist($room1);

        $user4 = $manager->getRepository(User::class)->findOneBy(['username' => 'user4']);
        $user5 = $manager->getRepository(User::class)->findOneBy(['username' => 'user5']);

        $room2 = new Room();
        $room2->setOwner($user4);
        $room2->addUser($user4);
        $room2->addUser($user5);
        $room2->setIsPublic(false);
        $manager->persist($room2);


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
