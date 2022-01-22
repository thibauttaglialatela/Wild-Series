<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASON_NB = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::SEASON_NB; $i++) {
            $season = new Season();
            $season->setYear($faker->year);
            $season->setDescription($faker->realText(200));
            $season->setNumber($i);
            $season->setProgram($this->getReference('program_0'));
            $this->addReference('season_' . $i, $season);
            $manager->persist($season);

        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }


}
