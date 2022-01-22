<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public const EPISODE_NB = 10;

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < self::EPISODE_NB; $i++) {
            for ($j = 0; $j < 10; $j++) {
                $episode = new Episode();
                $episode->setNumber($j);
                $episode->setTitle($faker->text(45));
                $episode->setSynopsis($faker->realText(200));
                $episode->setSlug($this->slugify->generate($episode->getTitle()));
                $episode->setSeason($this->getReference('season_' . $i));
                $manager->persist($episode);
            }
        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}
