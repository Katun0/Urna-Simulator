<?php

namespace App\Controller\Cadastro;

use App\Entity\Candidato;
use App\Form\CandidatoFormType;
use App\Repository\CandidatoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CandidatoController extends AbstractController
{
    #[Route('/cadastro/candidato', name: 'app_cadastro_candidato_index', methods: ['GET'])]
    public function index(CandidatoRepository $candidatoRepository): Response
    {
        return $this->render('cadastro/candidato/index.html.twig', [
            'candidatos' => $candidatoRepository->findAll(),
        ]);
    }

    #[Route('/cadastro/candidato/novo', name: 'app_cadastro_candidato_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $candidato = new Candidato();
        $form = $this->createForm(CandidatoFormType::class, $candidato);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fotoFile = $form->get('Foto')->getData();
            if ($fotoFile) {
                $candidato->setFoto(file_get_contents($fotoFile->getPathname()));
            }
            $entityManager->persist($candidato);
            $entityManager->flush();

            $this->addFlash('success', 'Candidato cadastrado com sucesso!');
            return $this->redirectToRoute('app_cadastro_candidato_index');
        }

        return $this->render('cadastro/candidato/new.html.twig', [
            'candidato' => $candidato,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cadastro/candidato/{id}/editar', name: 'app_cadastro_candidato_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidato $candidato, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatoFormType::class, $candidato);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fotoFile = $form->get('Foto')->getData();
            if ($fotoFile) {
                $candidato->setFoto(file_get_contents($fotoFile->getPathname()));
            }
            $entityManager->flush();

            $this->addFlash('success', 'Candidato atualizado com sucesso!');
            return $this->redirectToRoute('app_cadastro_candidato_index');
        }

        $fotoBase64 = null;
        if ($candidato->getFoto()) {
            $fotoContent = $candidato->getFoto();
            if (is_resource($fotoContent)) {
                $fotoContent = stream_get_contents($fotoContent);
            }
            $fotoBase64 = base64_encode($fotoContent);
        }

        return $this->render('cadastro/candidato/edit.html.twig', [
            'candidato' => $candidato,
            'fotoBase64' => $fotoBase64,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cadastro/candidato/{id}', name: 'app_cadastro_candidato_delete', methods: ['POST'])]
    public function delete(Request $request, Candidato $candidato, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidato->getId(), $request->request->get('_token'))) {
            $entityManager->remove($candidato);
            $entityManager->flush();
            $this->addFlash('success', 'Candidato removido com sucesso!');
        }

        return $this->redirectToRoute('app_cadastro_candidato_index');
    }
}
