<?php

namespace App\Form\Admin;

use App\Entity\Achieve;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AchieveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'attr'  => [
                    'class' => 'form-control md-textarea',
                    'rows'  => 3,
                ],
                'config' => ['height' => '200'],
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'query_builder' => function (ServiceRepository $er) {
                    return $er->getEnabledWithParentNull();
                },
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'label' => 'Service',
                'placeholder' => 'Service',
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Activé',
                'required' => false,
            ])
            ->add('file', VichImageType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Achieve::class,
        ]);
    }

}
