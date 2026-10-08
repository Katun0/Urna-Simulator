<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Repository\CustomRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        CustomRoleRepository $customRoleRepository
    ): Response {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set role to Votante by default
            $votanteRole = $customRoleRepository->findOneBy(['name' => 'ROLE_VOTANTE']);

            if (!$votanteRole) {
                // If ROLE_VOTANTE doesn't exist, try to find ANY role that has urna access
                // or just create/use a default one.
                // Given the instructions, we should ensure the user HAS the role.
                // For now, let's assume ROLE_VOTANTE is the intended name for voting permission.
                $votanteRole = new \App\Entity\CustomRole();
                $votanteRole->setName('ROLE_VOTANTE');
                $votanteRole->setAllowedRoutes(['urna_interface', 'api_candidatos', 'api_votar']);
                $entityManager->persist($votanteRole);
                $entityManager->flush(); // Flush to get ID if needed, though not strictly necessary for association
            }

            $user->setCustomRole($votanteRole);
            $user->setRoles(['ROLE_USER', 'ROLE_VOTANTE']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
