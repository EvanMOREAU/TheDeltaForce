<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Informations des articles
        $articlesData = [
            [
                'title' => 'Article 1',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'img' => '1.jpg'
            ],
            [
                'title' => 'Article 2',
                'text' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'img' => '2.jpg'
            ],
            [
                'title' => 'Article 3',
                'text' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'img' => '3.jpg'
            ],
            [
                'title' => 'Article 4',
                'text' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'img' => '4.jpg'
            ],
            [
                'title' => 'Article 5',
                'text' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.',
                'img' => '5.jpg'
            ],
        ];

        foreach ($articlesData as $data) {
            $article = new Articles();
            $article->setTitle($data['title']);
            $article->setText($data['text']);
            $article->setImg($data['img']);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
