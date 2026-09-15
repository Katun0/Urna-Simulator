<?php

namespace App\Controller\Urna;

use App\Repository\CandidatoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UrnaController extends AbstractController
{
    #[Route('/urna', name: 'urna_interface')]
    public function index(): Response
    {
        return $this->render('Urna/urnaInterface.html.twig');
    }

    #[Route('/api/candidatos/list', name: 'api_candidatos', methods: ['GET'])]
    public function getCandidatos(CandidatoRepository $candidatoRepository): JsonResponse
    {
        $candidatos = $candidatoRepository->findAll();
        $data = [];

        foreach ($candidatos as $candidato) {
            $data[] = [
                'numero' => $candidato->getNumero(),
                'nome' => $candidato->getNome(),
                'partido' => $candidato->getPartido() ? $candidato->getPartido()->getNome() : 'Sem Partido',
            ];
        }

        return new JsonResponse($data);
    }
}
