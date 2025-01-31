<?php

namespace App\DataFixtures;

use App\Entity\Games;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GamesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Informations des jeux
        $games = [
            ['name' => 'READY OR NOT', 'description' => 'Jeu de tir tactique axé sur les interventions des forces de l\'ordre.', 'img' => '1.jpg'],
            ['name' => 'Arma REFORGER', 'description' => 'Simulation militaire réaliste avec un accent sur le combat à grande échelle.', 'img' => '2.jpg'],
            ['name' => 'Arma 3', 'description' => 'Jeu de simulation militaire réaliste avec un vaste monde ouvert et des missions variées.', 'img' => '3.jpg'],
            ['name' => 'Squad', 'description' => 'Jeu de tir tactique multijoueur axé sur la coopération en équipe.', 'img' => '4.jpg'],
        ];

        foreach ($games as $gameData) {
            $game = new Games();
            $game->setName($gameData['name']);
            $game->setDescription($gameData['description']);
            $game->setImg($gameData['img']);
            $manager->persist($game);
        }

        $manager->flush();
    }
}
