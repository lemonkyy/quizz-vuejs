<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractFixtures extends Fixture
{
    protected ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $r = new \ReflectionClass($this->getEntityClass());

        foreach ($this->getData() as $key => $data) {
            $constructor = $r->getConstructor();
            $args = [];

            $entity = $constructor ? $r->newInstanceArgs($args) : $r->newInstance();

            foreach ($data as $property => $value) {
                $setter = 'set'.ucfirst($property);
                if (method_exists($entity, $setter)) {
                    $entity->$setter($value);
                }
            }

            $this->postInstantiate($entity, $data);
            $manager->persist($entity);
            ++$key;
            if (method_exists($this, 'addReference')) {
                $this->addReference($r->getShortName().'_'.$key, $entity);
                echo "Adding reference: {$r->getShortName()}_{$key}\n";

            }
        }

        $manager->flush();
    }

    public function getEM(): ObjectManager
    {
        return $this->manager;
    }

    protected function postInstantiate(object $entity, array $data): void
    {
    }

    abstract protected function getData(): iterable;

    abstract protected function getEntityClass(): string;
}
