<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TermsAndConditionsController extends AbstractController
{
    #[Route('/termes-et-conditions', name: 'app_terms_and_conditions')]
    public function index(): Response
    {
        return $this->render('terms_and_conditions/index.html.twig', []);
    }
}
