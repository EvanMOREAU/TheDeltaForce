<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Form\TagsType;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/temps/tags')]
final class TempsTagsController extends AbstractController
{
    #[Route(name: 'app_temps_tags_index', methods: ['GET'])]
    public function index(TagsRepository $tagsRepository): Response
    {
        return $this->render('temps_tags/index.html.twig', [
            'tags' => $tagsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_temps_tags_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tags();
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_temps_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('temps_tags/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_temps_tags_show', methods: ['GET'])]
    public function show(Tags $tag): Response
    {
        return $this->render('temps_tags/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_temps_tags_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_temps_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('temps_tags/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_temps_tags_delete', methods: ['POST'])]
    public function delete(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_temps_tags_index', [], Response::HTTP_SEE_OTHER);
    }
}
