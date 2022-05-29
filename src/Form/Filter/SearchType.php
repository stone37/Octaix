<?php

namespace App\Form\Filter;

use App\Entity\Service;
use App\Model\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $services = $em->getRepository(Service::class)->getServiceParentNull();

        $builder
            ->add('data', ChoiceType::class, [
                'choices' => $services,
                'label' => false,
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ])->setRequired(['em']);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
