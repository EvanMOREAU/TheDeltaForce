<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use App\Services\ImageUploaderHelper;
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
    
    #[Route('/', name: 'app_administration')]
    public function index(): Response
    {
        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
        ]);
    }

    #[Route('/mon-compte-administrateur', name: 'app_admin_account')]
    public function account(Request $request, EntityManagerInterface $entityManager): Response
    {
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
    
                return $this->redirectToRoute('app_administration', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/utilisateurs', name: 'app_administration_users')]
    public function indexUsers(UserRepository $userRepository): Response
    {
        return $this->render('administration/user.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    #[Route('/tags',name: 'app_tags_index', methods: ['GET'])]
    public function indexTags(TagsRepository $tagsRepository): Response
    {
        return $this->render('administration/tags.html.twig', [
            'tags' => $tagsRepository->findAll(),
        ]);
    }
}
