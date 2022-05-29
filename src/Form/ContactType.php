<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom et prénom',
                'constraints' => new NotBlank(),
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre numéro',
                'constraints' => new NotBlank(),
            ])
            ->add('content',  TextareaType::class, [
                'label' => 'Parlez-nous de votre projet',
                'attr' => [
                    'class' => 'md-textarea form-control',
                    'rows' => 6
                ],
                'constraints' => new NotBlank(),
            ])
            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'app-recaptchaToken']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
