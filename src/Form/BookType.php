<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'Tytuł',
                'attr' => [
                    'placeholder' => 'Piotruś Pan',
                ],
            ])
            ->add('isbn', null, [
                'label' => 'ISBN',
            ])
            ->add('releasedAt', null, [
                'label' => 'Wydano',
                'help' => 'Data wydania',
                'attr' => [
                    'placeholder' => '1 stycznia 2000 roku',
                ],
            ])
            ->add('available', null, [
                'label' => 'Egzemplarz jest dostępny',
            ])
            ->add('quantity', null, [
                'mapped' => true,
                'label' => 'Ilość w magazynie',
                'attr' => [
                    'min' => 0,
                    'max' => 10000,
                    'step' => 1,
                ],
            ])
            ->add('reserved', null, [
                'mapped' => true,
                'label' => 'Ilość rezerwacji',
                'attr' => [
                    'min' => 0,
                    'max' => 10000,
                    'step' => 1,
                ],
            ])
            ->add('coverType', ChoiceType::class, [
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'label' => 'Typ okładki',
                'choices' => [
                    'miękka' => 'soft',
                    'twarda' => 'hard',
                ],
            ])
            ->add('description', null, [
                'label' => 'Opis książki',
                'attr' => [
                    'rows' => 8,
                    'placeholder' => 'Książka opowiada o...',
                ],
            ])
            ->add('price', null, [
                'label' => 'Cena brutto',
                'help' => 'Cena w polskich złotych.'
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Okładka (bądź ilustracja)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                    ]),
                ],
            ])
            ->add('deleteCover', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Usuń obecną okładkę',
                'help' => 'Jeśli wybrałeś nową okładkę, możesz pominąć tę opcję'
            ])
            ->add('author', null, [
                'label' => 'Autor',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('t')->where('t.deletedAt IS NULL');
                },
            ])
            ->add('genres', null, [
                'label' => 'Gatunki',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('t')->where('t.deletedAt IS NULL');
                },
            ])
            ->add('tags', null, [
                'label' => 'Tagi',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('t')->where('t.deletedAt IS NULL');
                },
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
