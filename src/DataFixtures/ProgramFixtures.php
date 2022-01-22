<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Service\Slugify;
use Faker\Factory;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{

    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public const PROGRAM_NB = 6;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $user = new User();

        //création des séries

        for ($i = 0; $i < self::PROGRAM_NB; $i++) {
            $program = new Program();
            $program->setTitle($faker->realText(45));
            $program->setSynopsis($faker->realText());
            $program->setPoster($faker->imageUrl());
            $program->setCountry($faker->country());
            $program->setYear($faker->year());
            $program->setSlug($this->slugify->generate($program->getTitle()));
            $program->setOwner($this->getReference('contributor'));
            $program->setCategory($this->getReference('category_3'));
            for ($j = 0; $j < 4; $j++) {
                $program->addActor($this->getReference('actor_' . $j));
            }
            $this->addReference('program_' . $i, $program);
            $manager->persist($program);
        }

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [
            ActorFixtures::class,
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }


}
