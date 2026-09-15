<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CandidatoController extends AbstractController
{
    #[Route('/cadastro/candidato', name: 'app_cadastro_candidato')]
    public function index(): Response
    {
        return $this->render('cadastro/candidato/index.html.twig', [
            'controller_name' => 'Cadastro/CandidatoController',
        ]);
    }
}
