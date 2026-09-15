<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UsuarioController extends AbstractController
{
    #[Route('/cadastro/usuario', name: 'app_cadastro_usuario')]
    public function index(): Response
    {
        return $this->render('cadastro/usuario/index.html.twig', [
            'controller_name' => 'Cadastro/UsuarioController',
        ]);
    }
}
