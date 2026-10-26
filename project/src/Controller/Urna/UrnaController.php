<?php

namespace App\Controller\Urna;

use App\Entity\Configuracao;
use App\Entity\ParticipacaoVoto;
use App\Entity\Voto;
use App\Repository\CandidatoRepository;
use App\Repository\ConfiguracaoRepository;
use App\Repository\ParticipacaoVotoRepository;
use App\Repository\VotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Service\VoteReceiptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UrnaController extends AbstractController
{
    #[Route('/urna', name: 'urna_interface')]
    public function index(ConfiguracaoRepository $configRepository): Response
    {
        $config = $configRepository->findOneBy([]);
        $finalizada = $config ? $config->isEleicaoFinalizada() : false;

        return $this->render('Urna/urnaInterface.html.twig', [
            'finalizada' => $finalizada
        ]);
    }

    #[Route('/api/candidatos', name: 'api_candidatos', methods: ['GET'])]
    public function apiCandidatos(CandidatoRepository $candidatoRepository): JsonResponse
    {
        $candidatos = $candidatoRepository->findAll();
        $data = [];

        foreach ($candidatos as $candidato) {
            $fotoBase64 = null;
            if ($candidato->getFoto()) {
                $fotoContent = $candidato->getFoto();
                // Doctrine pode retornar recurso para BLOB dependendo da configuração
                if (is_resource($fotoContent)) {
                    $fotoContent = stream_get_contents($fotoContent);
                }
                $fotoBase64 = base64_encode($fotoContent);
            }

            $data[] = [
                'numero' => $candidato->getNumero(),
                'nome' => $candidato->getNome(),
                'partido' => $candidato->getPartido() ? $candidato->getPartido()->getNome() : 'Sem Partido',
                'foto' => $fotoBase64,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/votar', name: 'api_votar', methods: ['POST'])]
    public function votar(
        Request $request,
        CandidatoRepository $candidatoRepository,
        EntityManagerInterface $entityManager,
        ParticipacaoVotoRepository $participacaoRepository,
        ConfiguracaoRepository $configRepository,
        VoteReceiptService $receiptService
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Usuário não autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->isCsrfTokenValid('api_votar', $request->headers->get('X-CSRF-Token'))) {
            return new JsonResponse(['error' => 'Token de segurança inválido. Atualize a página e tente novamente.'], Response::HTTP_FORBIDDEN);
        }

        $config = $configRepository->findOneBy([]);
        if ($config && $config->isEleicaoFinalizada()) {
            return new JsonResponse(['error' => 'A eleição já foi encerrada.'], Response::HTTP_FORBIDDEN);
        }

        // Verifica se o usuário já votou
        if ($participacaoRepository->findOneBy(['usuario' => $user])) {
            return new JsonResponse(['error' => 'Você já realizou seu voto.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(['error' => 'Requisição inválida.'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_array($data) || !is_bool($data['isBranco'] ?? null)) {
            return new JsonResponse(['error' => 'Dados do voto inválidos.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $isBranco = $data['isBranco'];
        $numero = $data['numero'] ?? null;
        if (!$isBranco && (!is_string($numero) || !preg_match('/^\d{2}$/D', $numero))) {
            return new JsonResponse(['error' => 'Informe um número de candidato válido.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $voto = new Voto();
        $voto->setIsBranco($isBranco);

        if (!$isBranco) {
            $candidato = $candidatoRepository->findOneBy(['Numero' => $numero]);
            if (!$candidato) {
                // Voto nulo (candidato não encontrado)
                $voto->setCandidato(null);
            } else {
                $voto->setCandidato($candidato);
            }
        }

        $participacao = new ParticipacaoVoto();
        $participacao->setUsuario($user);
        $comprovante = $receiptService->issue($voto);
        $entityManager->persist($participacao);
        $entityManager->persist($voto);

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            return new JsonResponse(['error' => 'Você já realizou seu voto.'], Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse([
            'success' => true,
            'comprovante' => $comprovante,
            'data' => $voto->getDataVoto()->format('d/m/Y H:i:s')
        ]);
    }

    #[Route('/urna/analise', name: 'urna_analise')]
    public function analise(VotoRepository $votoRepository, CandidatoRepository $candidatoRepository, ConfiguracaoRepository $configRepository): Response
    {
        $config = $configRepository->findOneBy([]);
        $finalizada = $config ? $config->isEleicaoFinalizada() : false;

        $votos = $votoRepository->findAll();
        $candidatos = $candidatoRepository->findAll();

        $resultados = [];
        $totalVotos = count($votos);
        $votosBrancos = 0;
        $votosNulos = 0;

        foreach ($candidatos as $candidato) {
            $resultados[$candidato->getId()] = [
                'nome' => $candidato->getNome(),
                'numero' => $candidato->getNumero(),
                'partido' => $candidato->getPartido() ? $candidato->getPartido()->getNome() : 'Sem Partido',
                'votos' => 0
            ];
        }

        foreach ($votos as $voto) {
            if ($voto->isBranco()) {
                $votosBrancos++;
            } elseif ($voto->getCandidato() === null) {
                $votosNulos++;
            } else {
                $candidatoId = $voto->getCandidato()->getId();
                if (isset($resultados[$candidatoId])) {
                    $resultados[$candidatoId]['votos']++;
                }
            }
        }

        return $this->render('Urna/analise.html.twig', [
            'resultados' => $resultados,
            'totalVotos' => $totalVotos,
            'votosBrancos' => $votosBrancos,
            'votosNulos' => $votosNulos,
            'finalizada' => $finalizada,
        ]);
    }

    #[Route('/urna/finalizar', name: 'urna_finalizar', methods: ['POST'])]
    public function finalizar(Request $request, ConfiguracaoRepository $configRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $csrfToken = $request->request->get('_token');
        if (!is_string($csrfToken) || !$this->isCsrfTokenValid('urna_finalizar', $csrfToken)) {
            throw $this->createAccessDeniedException('Token de segurança inválido. Atualize a página e tente novamente.');
        }

        $config = $configRepository->findOneBy([]);
        if (!$config) {
            $config = new Configuracao();
        }

        $config->setEleicaoFinalizada(true);
        $entityManager->persist($config);
        $entityManager->flush();

        $this->addFlash('success', 'Eleição finalizada com sucesso!');

        return $this->redirectToRoute('urna_analise');
    }
}
