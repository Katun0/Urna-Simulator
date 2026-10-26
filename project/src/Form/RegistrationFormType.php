<?php

namespace App\Form;

use App\Entity\CustomRole;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nome', TextType::class, [
                'constraints' => [new NotBlank(), new Length(max: 255)],
            ])
            ->add('Email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email(), new Length(max: 254)],
            ])
            ->add('Idade', IntegerType::class, [
                'constraints' => [new NotBlank(), new Range(min: 1, max: 120)],
            ])
            ->add('TituloEleitor', TextType::class, [
                'label' => 'Título de Eleitor',
                'constraints' => [new NotBlank(), new Length(max: 20)],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'minlength' => 12],
                'constraints' => [
                    new NotBlank(
                        message: 'Por favor informe uma senha',
                    ),
                    new Length(
                        min: 12,
                        minMessage: 'Sua senha deve ter pelo menos {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
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
