<?php

namespace App\Form;

use App\Entity\CustomRole;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsuarioFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nome')
            ->add('Email')
            ->add('Idade')
            ->add('TituloEleitor')
            ->add('customRole', EntityType::class, [
                'class' => CustomRole::class,
                'choice_label' => 'name',
                'label' => 'Perfil de Acesso (Role)',
                'placeholder' => 'Selecione um perfil',
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Nova Senha (deixe em branco para não alterar)',
                'constraints' => [
                    new Length(
                        min: 6,
                        minMessage: 'Sua senha deve ter pelo menos {{ limit }} caracteres',
                        max: 4096,
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
