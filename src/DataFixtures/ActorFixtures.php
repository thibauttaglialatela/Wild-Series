<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture
{
    public const ACTEURS = [
        'Kit Harrington',
        'Emilia Clarke',
        'Sophie Turner',
        'Lena Headey',
    ];
    public function load(ObjectManager $manager)
    {
        foreach(self::ACTEURS as $key => $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);
            $manager->persist($actor);
            $this->addReference('actor_' . $key, $actor);
        }
        

        $manager->flush();
    }
}
