<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Tags;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Games;
use App\Entity\Grades;
use App\Form\NewsType;
use App\Form\TagsType;
use App\Form\UserType;
use App\Entity\Comment;
use App\Form\EventType;
use App\Form\GamesType;
use App\Entity\Articles;
use App\Form\GradesType;
use App\Form\ArticlesType;
use App\Form\EventEditType;
use App\Form\GamesEditType;
use App\Form\ArticlesEditType;
use App\Repository\NewsRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use App\Form\GradesAffectationType;
use App\Repository\EventRepository;
use App\Repository\GamesRepository;
use App\Repository\GradesRepository;
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

#[Route('/administration')]
final class AdministrationController extends AbstractController
{
    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher, ImageUploaderHelper $imageUploaderHelper)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
        $this->imageUploaderHelper = $imageUploaderHelper;
    }

    #[Route(name: 'app_administration_users')]
    public function indexUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        return $this->render('administration/user.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    #[Route('/mon-compte-administrateur', name: 'app_admin_account')]
    public function account(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
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
    
                return $this->redirectToRoute('app_administration_users', [], Response::HTTP_SEE_OTHER);
            } else {
                // Si le mot de passe est invalide, ajouter un message d'erreur
                $this->addFlash('error', 'Mot de passe incorrect.');
            }
        }
    

        return $this->render('administration/account-profile.html.twig', [
            'controller_name' => 'AdministrationController',
            'UserIP' => $userIp,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_administration_users', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/tags',name: 'app_tags_index', methods: ['GET', 'POST'])]
    public function indexTags(TagsRepository $tagsRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $tag = new Tags();
        $formNew = $this->createForm(TagsType::class, $tag);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/tags.html.twig', [
            'tags' => $tagsRepository->findAll(),
            'formNew' => $formNew,
        ]);
    }
    // #[Route('/tags/{id}/delete', name: 'app_tags_delete', methods: ['POST'])]
    // public function delete(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($tag);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
    // }
    #[Route('/tags/{id}/edit', name: 'app_tags_edit', methods: ['GET', 'POST'])]
    public function editTag(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'd\'un Tag',
            'form' => $form,
        ]);
    }
    #[Route('/jeux',name: 'app_games_index', methods: ['GET','POST'])]
    public function indexGame(Request $request, GamesRepository $gamesRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $game = new Games();
        $formNew = $this->createForm(GamesType::class, $game);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $imageFile = $formNew->get('img')->getData();
            if ($imageFile) {
                dump($imageFile); // Vérifiez si le fichier est bien reçu
                try {
                    // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                    $newFilename = $this->imageUploaderHelper->uploadImage($imageFile, $game->getName());
                    if ($newFilename) {
                        $game->setImg($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
            $imageFile2 = $formNew->get('img2')->getData();
            if ($imageFile2) {
                dump($imageFile2); // Vérifiez si le fichier est bien reçu
                try {
                    // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                    $newFilename = $this->imageUploaderHelper->uploadImage($imageFile2, $game->getName());
                    if ($newFilename) {
                        $game->setImg2($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/games.html.twig', [
            'games' => $gamesRepository->findAll(),
            'formNew' => $formNew,
        ]);
    }
    #[Route('/jeu/{id}/delete', name: 'app_games_delete', methods: ['POST'])]
    public function deleteGame(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/jeu/{id}/edit', name: 'app_games_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(GamesEditType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $formNew->get('img')->getData();
            if ($imageFile) {
                dump($imageFile); // Vérifiez si le fichier est bien reçu
                try {
                    // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                    $newFilename = $this->imageUploaderHelper->uploadImage($imageFile, $game->getName());
                    if ($newFilename) {
                        $game->setImg($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
            $imageFile2 = $formNew->get('img2')->getData();
            if ($imageFile2) {
                dump($imageFile2); // Vérifiez si le fichier est bien reçu
                try {
                    // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                    $newFilename = $this->imageUploaderHelper->uploadImage($imageFile2, $game->getName());
                    if ($newFilename) {
                        $game->setImg($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'd\'un Jeu',
            'game' => $game,
            'form' => $form,
        ]);
    }
    #[Route('/articles', name: 'app_articles_index', methods: ['GET', 'POST'])]
    public function indexArticles(Request $request, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $article = new Articles();
        $formNew = $this->createForm(ArticlesType::class, $article);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();


            return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('administration/articles.html.twig', [
            'articles' => $articlesRepository->findAll(),
            'formNew' => $formNew,
        ]);
    }

    #[Route('/articles/{id}/delete', name: 'app_articles_delete', methods: ['POST'])]
    public function deleteArticles(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/articles/{id}/edit', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function editArticles(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(ArticlesEditType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'd\'un Article',
            'article' => $article,
            'form' => $form,
        ]);
    }
    #[Route('/grades/{id}/edit', name: 'app_grades_edit', methods: ['GET', 'POST'])]
    public function editgrades(Request $request, Grades $grade, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(GradesType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'd\'un grade',
            'grade' => $grade,
            'form' => $form,
        ]);
    }
    #[Route('/grades/{id}/delete', name: 'app_grades_delete', methods: ['POST'])]
    public function deleteGrades(Request $request, Grades $grade, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$grade->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($grade);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/grades', name: 'app_grades_index', methods: ['GET', 'POST'])]
    public function indexGrades(Request $request, GradesRepository $gradesRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $grade = new Grades();
        $formNew = $this->createForm(GradesType::class, $grade);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $entityManager->persist($grade);
            $entityManager->flush();


            return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('administration/grades.html.twig', [
            'grades' => $gradesRepository->findAll(),
            'formNew' => $formNew,
        ]);
    }

    #[Route('/grades/{id}/affectation', name: 'app_grades_affectation', methods: ['GET', 'POST'])]
    public function affectationGrade(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(GradesAffectationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_administration_users', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'du grade d\'un utilisateur',
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/news', name: 'app_news_index', methods: ['GET', 'POST'])]
    public function indexNews(Request $request, NewsRepository $newsRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $new = new News();
        $formNew = $this->createForm(NewsType::class, $new);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $entityManager->persist($new);
            $entityManager->flush();


            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('administration/news.html.twig', [
            'news' => $newsRepository->findAll(),
            'formNew' => $formNew,
        ]);
    }

    #[Route('/news/{id}/edit', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function editNews(Request $request, News $new, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(NewsType::class, $new);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'd\'un New',
            'new' => $new,
            'form' => $form,
        ]);
    }
    #[Route('/news/{id}/delete', name: 'app_news_delete', methods: ['POST'])]
    public function deleteNews(Request $request, News $new, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$new->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($new);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/events', name: 'app_events_index', methods: ['GET', 'POST'])]
    public function indexEvent(Request $request, EventRepository $eventsRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $event = new Event();
        $formNew = $this->createForm(EventType::class, $event);
        $formNew->handleRequest($request);

        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $imageFile = $formNew->get('img')->getData();
            if ($imageFile) {
                dump($imageFile); // Vérifiez si le fichier est bien reçu
                try {
                    // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                    $newFilename = $this->imageUploaderHelper->uploadImage($imageFile, $event->getTitle());
                    if ($newFilename) {
                        $event->setImg($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
            $user = $this->security->getUser();
            $event->setAuthor($user);
            $entityManager->persist($event);
            $entityManager->flush();


            return $this->redirectToRoute('app_events_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('administration/event.html.twig', [
            'events' => $eventsRepository->findAll(),
            'formNew' => $formNew,
        ]);
    }

    #[Route('/events/{id}/edit', name: 'app_events_edit', methods: ['GET', 'POST'])]
    public function editEvent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $form = $this->createForm(EventEditType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('img')->getData();
            if ($imageFile) {
                dump($imageFile); // Vérifiez si le fichier est bien reçu
                try {
                    // Appeler la méthode uploadImage pour obtenir le nom du fichier final
                    $newFilename = $this->imageUploaderHelper->uploadImage($imageFile, $event->getTitle());
                    if ($newFilename) {
                        $event->setImg($newFilename); // Enregistrer seulement le nom du fichier dans l'entité
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administration/edit.html.twig', [
            'entityInfo' => 'd\'un Event',
            'event' => $event,
            'form' => $form,
        ]);
    }
    #[Route('/commentaires', name: 'app_comment_index', methods: ['GET', 'POST'])]
    public function indexComment(Request $request, CommentRepository $commentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        
        return $this->render('administration/comment.html.twig', [
            'Comments' => $commentRepository->findAll(),
        ]);
    }
    #[Route('/commentaire/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
    // #[Route('/events/{id}/delete', name: 'app_events_delete', methods: ['POST'])]
    // public function deleteEvent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    //     $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    //     if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($event);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_events_index', [], Response::HTTP_SEE_OTHER);
    // }
}
