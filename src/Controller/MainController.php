<?php

namespace App\Controller;

use App\Repository\GamesRepository;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MainController extends AbstractController
{
    #[Route('/', name : "app_main")]
    public function index(GamesRepository $gamesRepository, ArticlesRepository $articlesRepository): Response
    {

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'games' => $gamesRepository->findAll(),
            'articles' => $articlesRepository->findAll(),
        ]);
    }
}
