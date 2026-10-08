<?php

namespace App\Form;

use App\Entity\CustomRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

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
