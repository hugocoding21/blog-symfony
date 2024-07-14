<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i = 0; $i < 10; $i++) {
            $place = new Place();
            $place->setCity($faker->city());
            $manager->persist($place);
            $this->addReference("place-" . $i, $place);
        }
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word());
            $manager->persist($category);
            $this->addReference("category-" . $i, $category);
        }

        $names = ["Concert", "Cinéma", "Théatre"];

        foreach ($names as $name) {
            $event = new Event();
            $event->setName($name);
            $event->setPrice($faker->randomfloat(2, 10, 100));
            $event->setStartAt(
                $startAt = $faker->dateTimeBetween("-30 days", "+ 30 days")
            );
            $event->setEndAt(
                (clone $startAt)->modify("+" . rand(0, 48) . " hours")
            );
            $event->setPlace($this->getReference("place-" . rand(0, 9)));

            $keys = (array) array_rand(range(0, 9), rand(1, 3));
            foreach ($keys as $key) {
                $event->addCategory($this->getReference("category-" . $key));
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
}
