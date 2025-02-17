<?php

namespace App\DataFixtures;

use App\Entity\Grades;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GradesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Noms et niveaux des grades de l'armée américaine traduits en français
        $grades = [
            ['name' => 'Soldat', 'levelRank' => 1],
            ['name' => 'Recrue', 'levelRank' => 2],
            ['name' => 'Caporal', 'levelRank' => 3],
            ['name' => 'Sergent', 'levelRank' => 4],
            ['name' => 'Adjudant', 'levelRank' => 5],
            ['name' => 'Lieutenant', 'levelRank' => 6],
            ['name' => 'Capitaine', 'levelRank' => 7],
            ['name' => 'Commandant', 'levelRank' => 8],
        ];


        foreach ($grades as $gradeData) {
            $grade = new Grades();
            $grade->setName($gradeData['name']);
            $grade->setLevelRank($gradeData['levelRank']);
            $manager->persist($grade);
        }

        $manager->flush();
    }
}
