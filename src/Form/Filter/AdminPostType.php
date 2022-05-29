<?php

namespace App\Form\Filter;

use App\Entity\Category;
use App\Model\Admin\PostSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $categories = $em->getRepository(Category::class)->getCategory();

        $builder
            ->add('published', CheckboxType::class, ['label' => 'Publier', 'required' => false])
            ->add('category', ChoiceType::class, [
                'choices' => $categories,
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone mb-0',
                ],
                'required' => false,
                'placeholder' => 'Catégorie',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ])->setRequired('em');
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
