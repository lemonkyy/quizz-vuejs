<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends AbstractFixtures
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
        ];

        yield [
            'email' => 'user2@example.com',
            'username' => 'user2',
            'password' => 'user2',
            'roles' => ['ROLE_USER'],
        ];
    }

    protected function postInstantiate($entity): void
    {
        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));
    }
}
