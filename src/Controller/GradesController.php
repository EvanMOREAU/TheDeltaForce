<?php

namespace App\Controller;

use App\Entity\Grades;
use App\Form\GradesType;
use App\Repository\GradesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/grades')]
final class GradesController extends AbstractController
{
    // #[Route(name: 'app_grades_index', methods: ['GET'])]
    // public function index(GradesRepository $gradesRepository): Response
    // {
    //     return $this->render('grades/index.html.twig', [
    //         'grades' => $gradesRepository->findAll(),
    //     ]);
    // }

    // #[Route('/new', name: 'app_grades_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $grade = new Grades();
    //     $form = $this->createForm(GradesType::class, $grade);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($grade);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('grades/new.html.twig', [
    //         'grade' => $grade,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_grades_show', methods: ['GET'])]
    // public function show(Grades $grade): Response
    // {
    //     return $this->render('grades/show.html.twig', [
    //         'grade' => $grade,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_grades_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Grades $grade, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(GradesType::class, $grade);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('grades/edit.html.twig', [
    //         'grade' => $grade,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_grades_delete', methods: ['POST'])]
    // public function delete(Request $request, Grades $grade, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$grade->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($grade);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_grades_index', [], Response::HTTP_SEE_OTHER);
    // }
}
