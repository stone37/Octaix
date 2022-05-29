<?php

namespace App\Form;

use App\Dto\ProfileUpdateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method ProfileUpdateDto getData()
 */
class UpdateProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('username', TextType::class, [
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
            ])
            ->add('phoneStatus', CheckboxType::class, [
                'label' => 'Numéro whatsapp',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileUpdateDto::class,
        ]);
    }
}
