<?php

namespace App\Form\Admin;

use App\Entity\Offer;
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

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
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
                    return $er->getEnabled();
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
            'data_class' => Offer::class,
        ]);
    }

}
