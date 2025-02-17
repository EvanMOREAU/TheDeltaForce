<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste des tags
        // $tagsData = [
        //     ['name' => 'Action', 'color' => '#FF5733'],
        //     ['name' => 'Aventure', 'color' => '#33FF57'],
        //     ['name' => 'Stratégie', 'color' => '#3357FF'],
        //     ['name' => 'Simulation', 'color' => '#FF33A1'],
        //     ['name' => 'RPG', 'color' => '#FF8333'],
        //     ['name' => 'Course', 'color' => '#57FF33'],
        //     ['name' => 'Sport', 'color' => '#33FFF5'],
        //     ['name' => 'Puzzle', 'color' => '#FF3333'],
        //     ['name' => 'Horreur', 'color' => '#FF33FF'],
        //     ['name' => 'Aventure narrative', 'color' => '#33FF8F'],
        // ];

        // foreach ($tagsData as $data) {
        //     $tag = new Tags();
        //     $tag->setName($data['name']);
        //     $tag->setColor($data['color']);
        //     $manager->persist($tag);
        // }

        // $manager->flush();
    }
}
