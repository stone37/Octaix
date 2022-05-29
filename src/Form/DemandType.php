<?php

namespace App\Form;

use App\Entity\Demand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom *'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom *'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone *',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
            ])
            ->add('numberLocale', ChoiceType::class, [
                'label' => 'Nombre de locale *',
                'choices' => [
                    '1' => '1',
                    '2-5' => '2-5',
                    '+ de 5' => '+ de 5',
                ],
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'placeholder' => 'Nombre de locale *',
            ])
            ->add('location', LocationType::class, [
                'label' => 'Localisation',
                'em' => $em,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demand::class,
        ]);
    }
}
