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
            ['name' => 'Soldat de Première Classe', 'levelRank' => 2],
            ['name' => 'Caporal', 'levelRank' => 3],
            ['name' => 'Sergent', 'levelRank' => 4],
            ['name' => 'Sergent-Chef', 'levelRank' => 5],
            ['name' => 'Sergent de Première Classe', 'levelRank' => 6],
            ['name' => 'Sergent-Major', 'levelRank' => 7],
            ['name' => 'Premier Sergent', 'levelRank' => 8],
            ['name' => 'Sergent-Major de Commandement', 'levelRank' => 9],
            ['name' => 'Sergent-Major de l\'Armée', 'levelRank' => 10],
            ['name' => 'Sous-Lieutenant', 'levelRank' => 11],
            ['name' => 'Lieutenant', 'levelRank' => 12],
            ['name' => 'Capitaine', 'levelRank' => 13],
            ['name' => 'Commandant', 'levelRank' => 14],
            ['name' => 'Lieutenant-Colonel', 'levelRank' => 15],
            ['name' => 'Colonel', 'levelRank' => 16],
            ['name' => 'Général de Brigade', 'levelRank' => 17],
            ['name' => 'Général de Division', 'levelRank' => 18],
            ['name' => 'Général de Corps d\'Armée', 'levelRank' => 19],
            ['name' => 'Général d\'Armée', 'levelRank' => 20],
            ['name' => 'Général des Armées', 'levelRank' => 21],
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
