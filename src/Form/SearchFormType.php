<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction('/')
            ->setMethod('GET')
            ->add('title', null, [
                'required' => false,
                'mapped' => false,
                'label' => 'Tytuł',
            ])
            ->add('price', null, [
                'required' => false,
                'mapped' => false,
                'label' => 'Cena',
            ])
            ->add('author', null, [
                'required' => false,
                'mapped' => false,
                'label' => 'Autor',
            ])
            ->add('genres', null, [
                'required' => false,
                'mapped' => false,
                'label' => 'Gatunek',
            ])
            ->add('tags', null, [
                'required' => false,
                'mapped' => false,
                'label' => 'Tagi',
            ])
            ->add('releasedAt', null, [
                'required' => false,
                'mapped' => false,
                'label' => 'Wydano od',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
