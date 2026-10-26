<?php

namespace App\Form;

use App\Entity\CustomRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomRoleFormType extends AbstractType
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $routeCollection = $this->router->getRouteCollection();
        $routes = [];
        foreach ($routeCollection->all() as $name => $route) {
            if (str_starts_with($name, '_')) {
                continue;
            }
            $routes[$name] = $name;
        }
        ksort($routes);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome da Role (ex: ROLE_MODERADOR)',
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 100),
                    new Regex(pattern: '/^ROLE_[A-Z][A-Z0-9_]*$/', message: 'Use um identificador no formato ROLE_NOME.'),
                ],
            ])
            ->add('routes', ChoiceType::class, [
                'label' => 'Rotas Permitidas',
                'choices' => $routes,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomRole::class,
        ]);
    }
}
