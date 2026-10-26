<?php

namespace App\Form;

use App\Entity\Candidato;
use App\Entity\Partido;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nome', TextType::class, [
                'constraints' => [new NotBlank(), new Length(max: 255)],
            ])
            ->add('Numero', TextType::class, [
                'constraints' => [new NotBlank(), new Length(max: 255)],
            ])
            ->add('Partido', EntityType::class, [
                'class' => Partido::class,
                'choice_label' => 'Nome',
            ])
            ->add('Foto', FileType::class, [
                'label' => 'Foto',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Envie uma imagem JPEG, PNG ou WebP válida.',
                    ),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidato::class,
        ]);
    }
}
