<?php

namespace App\Form\Filter;

use App\Entity\Service;
use App\Model\Admin\AchieveSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminAchieveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $services = $em->getRepository(Service::class)->getEnabledData();

        $builder
            ->add('service', ChoiceType::class, [
                'choices' => $services,
                'label' => 'Services',
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'required' => false,
                'placeholder' => 'Services',
            ])
            ->add('enabled', CheckboxType::class, ['label' => 'Activé', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AchieveSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ])->setRequired(['em']);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
