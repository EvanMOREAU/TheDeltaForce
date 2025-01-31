<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\NewsRepository;
use App\Repository\EventRepository;
use App\Repository\GamesRepository;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MainController extends AbstractController
{
    #[Route('/', name : "app_main")]
    public function index(GamesRepository $gamesRepository, ArticlesRepository $articlesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository): Response
    {

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'games' => $gamesRepository->findAll(),
            'articles' => $articlesRepository->findAll(),
            'news' => $newsRepository->findAll(),
            'pastEvents' => $eventsRepository->findPastEvents(),
            'upcomingEvents' => $eventsRepository->findUpcomingEvents(),
        ]);
    }

    #[Route('/nos-jeux', name : "app_games")]
    public function games(GamesRepository $gamesRepository, ArticlesRepository $articlesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository): Response
    {

        return $this->render('main/games.html.twig', [
            'games' => $gamesRepository->findAll(),
            'news' => $newsRepository->findAll(),
            'topGames' => $gamesRepository->findTopFourById(),
        ]);
    }

    #[Route('/articles', name : "app_blog")]
    public function blog(GamesRepository $gamesRepository, ArticlesRepository $articlesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository): Response
    {
        $lastGame = $gamesRepository->findLastById();

        return $this->render('main/blog.html.twig', [
            'controller_name' => 'MainController',
            'game' => $lastGame[0],
            'articles' => $articlesRepository->findAll(),
            'news' => $newsRepository->findAll(),
        ]);
    }

    #[Route('/articles/{id}', name : "app_blog_article", methods: ['POST', 'GET'])]
    public function blogArticle(GamesRepository $gamesRepository, Articles $article, NewsRepository $newsRepository, EventRepository $eventsRepository): Response
    {

        return $this->render('main/article.html.twig', [
            'controller_name' => 'MainController',
            'games' => $gamesRepository->findAll(),
            'article' => $article,
            'news' => $newsRepository->findAll(),
            'pastEvents' => $eventsRepository->findPastEvents(),
            'upcomingEvents' => $eventsRepository->findUpcomingEvents(),
        ]);
    }

}
