<?php

namespace App\DataFixtures;

use App\Entity\ProfilePicture;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function getEntityClass(): string
    {
        return User::class;
    }

    public function getData(): iterable
    {
        yield [
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN'],
        ];

        yield [
            'email' => 'user1@example.com',
            'username' => 'user1',
            'password' => 'user1',
            'roles' => ['ROLE_USER'],
            // gg-ignore
            'totpSecret' => 'NOO4I7MLZ6UZMJLIWMM6TKRSYM',
        ];

        yield [
            'email' => 'user2@example.com',
            'username' => 'user2',
            'password' => 'user2',
            'roles' => ['ROLE_USER'],
        ];

        yield [
            'email' => 'user3@example.com',
            'username' => 'user3',
            'password' => 'user3',
            'roles' => ['ROLE_USER'],
        ];

        yield [
            'email' => 'user4@example.com',
            'username' => 'user4',
            'password' => 'user4',
            'roles' => ['ROLE_USER'],
        ];

        yield [
            'email' => 'user5@example.com',
            'username' => 'user5',
            'password' => 'user5',
            'roles' => ['ROLE_USER'],
        ];

        yield [
            'email' => 'user6@example.com',
            'username' => 'user6',
            'password' => 'user6',
            'roles' => ['ROLE_USER'],
        ];

        $faker = Factory::create();
        for ($i = 0; $i < 50; $i++) {
            yield [
                'email' => $faker->unique()->safeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ];
        }
    }

    protected function postInstantiate(object $entity, array $data): void
    {
        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));

        $randomNumber = rand(1, 3);
        $randomReferenceKey = 'ProfilePicture_' . $randomNumber;
        $profilePicture = $this->getReference($randomReferenceKey, ProfilePicture::class);
        $entity->setProfilePicture($profilePicture);
    }

    public function getDependencies(): array
    {
        return [
            ProfilePictureFixtures::class,
        ];
    }
}
