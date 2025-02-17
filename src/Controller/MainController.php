<?php

namespace App\Controller;

use DateTime;
use App\Form\UserType;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Articles;
use App\Form\CommentType;
use App\Repository\NewsRepository;
use App\Repository\EventRepository;
use App\Repository\GamesRepository;
use App\Repository\CommentRepository;
use App\Services\ImageUploaderHelper;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class MainController extends AbstractController
{
    private $security;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher, ImageUploaderHelper $imageUploaderHelper)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
        $this->imageUploaderHelper = $imageUploaderHelper;
    }
    
    #[Route('/', name : "app_main")]
    public function index(GamesRepository $gamesRepository, ArticlesRepository $articlesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository, CommentRepository $commentRepository): Response
    {

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'games' => $gamesRepository->findAll(),
            'articles' => $articlesRepository->findAll(),
            'news' => $newsRepository->findAll(),
            'pastEvents' => $eventsRepository->findPastEvents(),
            'upcomingEvents' => $eventsRepository->findUpcomingEvents(),
            'lastArticles' => $articlesRepository->findLatestArticlesById(),
            'lastComments' => $commentRepository->findLatestComments(),
        ]);
    }

    #[Route('/nos-jeux', name : "app_games")]
    public function games(GamesRepository $gamesRepository, ArticlesRepository $articlesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository, CommentRepository $commentRepository): Response
    {

        return $this->render('main/games.html.twig', [
            'games' => $gamesRepository->findAll(),
            'news' => $newsRepository->findAll(),
            'topGames' => $gamesRepository->findTopFourById(),
            'lastArticles' => $articlesRepository->findLatestArticlesById(),
            'lastComments' => $commentRepository->findLatestComments(),
        ]);
    }

    #[Route('/articles', name : "app_blog")]
    public function blog(GamesRepository $gamesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository, ArticlesRepository $articlesRepository, CommentRepository $commentRepository): Response
    {
        $lastGame = $gamesRepository->findLastById();

        return $this->render('main/blog.html.twig', [
            'controller_name' => 'MainController',
            'game' => $lastGame[0],
            'articles' => $articlesRepository->findAll(),
            'news' => $newsRepository->findAll(),
            'lastArticles' => $articlesRepository->findLatestArticlesById(),
            'lastComments' => $commentRepository->findLatestComments(),
        ]);
    }

    #[Route('/articles/{id}', name : "app_blog_article", methods: ['POST', 'GET'])]
    public function blogArticle(GamesRepository $gamesRepository, Articles $article, ArticlesRepository $articlesRepository, NewsRepository $newsRepository, EventRepository $eventsRepository, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $lastGame = $gamesRepository->findLastById();

        $user = $this->security->getUser();
        $comment = new Comment();
        $formNew = $this->createForm(CommentType::class, $comment);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $comment->setAuthor($user);
            $comment->setArticle($article);
            $comment->setDate(new DateTimeImmutable());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_article', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/article.html.twig', [
            'controller_name' => 'MainController',
            'games' => $gamesRepository->findAll(),
            'article' => $article,
            'formNew' => $formNew,
            'game' => $lastGame[0],
            'lastComments' => $commentRepository->findLatestComments(),
            'comments' => $article->getComments(),
            'news' => $newsRepository->findAll(),
            'pastEvents' => $eventsRepository->findPastEvents(),
            'lastArticles' => $articlesRepository->findLatestArticlesById(),
            'upcomingEvents' => $eventsRepository->findUpcomingEvents(),
        ]);
    }
    
    #[Route('/mon-compte', name: 'app_main_account')]
    public function account(Request $request, EntityManagerInterface $entityManager, ArticlesRepository $articlesRepository, CommentRepository $commentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $userIp = $request->getClientIp();
    
        $user = $this->security->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'ancien mot de passe saisi
            $oldPassword = $form->get('password')->getData();
    
            // Vérifier le mot de passe avec isPasswordValid
            if ($this->passwordHasher->isPasswordValid($user, $oldPassword)) {
                // Appliquer les changements et enregistrer
                $newPassword = $form->get('plainPassword')->getData();
                if ($newPassword) {
                    $hashedNewPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedNewPassword);
                }
                $imageFile = $form->get('img')->getData();
                if ($imageFile) {
                    dump($imageFile); // Vérifiez si le fichier est bien reçu
                    try {
                        // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                        $newFilename = $this->imageUploaderHelper->uploadImage($imageFile, $user->getId());
                        if ($newFilename) {
                            $user->setImg($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                        }
                    } catch (\Exception $e) {
                        $this->addFlash('danger', $e->getMessage());
                    }
                }
                $entityManager->flush();
    
                return $this->redirectToRoute('app_main_account', [], Response::HTTP_SEE_OTHER);
            } else {
                // Si le mot de passe est invalide, ajouter un message d'erreur
                $this->addFlash('error', 'Mot de passe incorrect.');
            }
        }
    

        return $this->render('main/account-profile.html.twig', [
            'controller_name' => 'AdministrationController',
            'UserIP' => $userIp,
            'user' => $user,
            'lastArticles' => $articlesRepository->findLatestArticlesById(),
            'lastComments' => $commentRepository->findLatestComments(),
            'form' => $form->createView(),
        ]);
    }

}
