<?php

namespace App\Controller\Cadastro;

use App\Entity\Usuario;
use App\Form\UsuarioFormType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cadastro/usuario')]
class UsuarioController extends AbstractController
{
    #[Route('/', name: 'app_cadastro_usuario_index', methods: ['GET'])]
    public function index(UsuarioRepository $usuarioRepository): Response
    {
        return $this->render('cadastro/usuario/index.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cadastro_usuario_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Usuario $usuario,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $form = $this->createForm(UsuarioFormType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $usuario->setPassword($userPasswordHasher->hashPassword($usuario, $plainPassword));
            }

            // Sync roles based on CustomRole
            $roles = ['ROLE_USER'];
            if ($usuario->getCustomRole()) {
                $roles[] = $usuario->getCustomRole()->getName();
            }
            $usuario->setRoles($roles);

            $entityManager->flush();

            $this->addFlash('success', 'Usuário atualizado com sucesso!');
            return $this->redirectToRoute('app_cadastro_usuario_index');
        }

        return $this->render('cadastro/usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cadastro_usuario_delete', methods: ['POST'])]
    public function delete(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($usuario);
            $entityManager->flush();
            $this->addFlash('success', 'Usuário removido com sucesso!');
        }

        return $this->redirectToRoute('app_cadastro_usuario_index');
    }
}
