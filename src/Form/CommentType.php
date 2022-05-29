<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Votre nom d\'utilisateur *'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre mail *'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire *',
                'attr' => ['rows' => 6],
            ])
            ->add('rgpd',  CheckboxType::class, [
                'label' => 'J\'accepte que mes informations soient stockées dans 
                la base de données pour la gestion des commentaires. 
                J\'ai bien noté qu\'en aucun cas ces données ne seront cédées à des tiers.',
                'mapped' => false,
                'data' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'app-recaptchaToken']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}



