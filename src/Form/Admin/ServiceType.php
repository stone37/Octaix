<?php

namespace App\Form\Admin;

use App\Entity\Service;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'attr'  => [
                    'class' => 'form-control md-textarea',
                    'rows'  => 3,
                ],
                'config' => ['height' => '200'],
            ])
            ->add('isHome', CheckboxType::class, [
                'label' => 'Mettre en avant',
                'required' => false
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Activé',
                'required' => false
            ])
            ->add('file', VichImageType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
