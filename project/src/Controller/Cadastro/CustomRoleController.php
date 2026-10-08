<?php

namespace App\Controller\Cadastro;

use App\Entity\CustomRole;
use App\Form\CustomRoleFormType;
use App\Repository\CustomRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cadastro/role')]
class CustomRoleController extends AbstractController
{
    #[Route('/', name: 'app_cadastro_role_index', methods: ['GET'])]
    public function index(CustomRoleRepository $customRoleRepository): Response
    {
        return $this->render('cadastro/role/index.html.twig', [
            'custom_roles' => $customRoleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cadastro_role_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customRole = new CustomRole();
        $form = $this->createForm(CustomRoleFormType::class, $customRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customRole);
            $entityManager->flush();

            return $this->redirectToRoute('app_cadastro_role_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cadastro/role/new.html.twig', [
            'custom_role' => $customRole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cadastro_role_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CustomRole $customRole, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomRoleFormType::class, $customRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cadastro_role_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cadastro/role/edit.html.twig', [
            'custom_role' => $customRole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cadastro_role_delete', methods: ['POST'])]
    public function delete(Request $request, CustomRole $customRole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customRole->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($customRole);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cadastro_role_index', [], Response::HTTP_SEE_OTHER);
    }
}
