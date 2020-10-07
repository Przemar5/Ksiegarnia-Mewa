<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'przykład@domena.org',
                ],
            ])
            ->add('name', null, [
                'label' => 'Imię',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Jan',
                ],
            ])
            ->add('surname', null, [
                'label' => 'Nazwisko',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nowak',
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => 'Płeć',
                'choices' => [
                    'mężczyzna' => 'male',
                    'kobieta' => 'female',
                    'inna' => 'other',
                ],
            ])
            ->add('birth', DateType::class, [
                'label' => 'Data urodzenia',
                'required' => true,
                'years' => range(1900, 2015),
            ])
            ->add('country', null, [
                'label' => 'Kraj',
                'attr' => [
                    'placeholder' => 'Nibylandia',
                ],
            ])
            ->add('city', null, [
                'label' => 'Miasto',
                'attr' => [
                    'placeholder' => 'Gdziesiowo',
                ],
            ])
            ->add('address', null, [
                'label' => 'Adres zamieszkania',
                'attr' => [
                    'placeholder' => 'ul. Przykładowa 1/2',
                ],
            ])
            ->add('postalCode', null, [
                'label' => 'Kod pocztowy',
                'attr' => [
                    'placeholder' => '12-345',
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Telefon kontaktowy',
                'required' => true,
                'help' => 'Format "123-456-789" lub "+48 123-456-789".',
                'attr' => [
                    'placeholder' => '123-456-789',
                ],
            ])
            ->add('additionalPhone', TelType::class, [
                'required' => false,
                'label' => 'Dodatkowy telefon (opcjonalny)',
                'help' => 'Format ten sam co powyżej.',
                'attr' => [
                    'placeholder' => '123-456-789',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Musisz zaakceptować warunki umowy, aby móc się zarejestrować.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'help' => 'Minimum 8 znaków, 1 mała litera, 1 duża, 1 cefra i 1 znak specjalny.',
                'mapped' => true,
                'invalid_message' => 'Oba hasła muszą być identyczne.',
                'first_options' => [
                    'label' => 'Hasło',
                ],
                'second_options' => [
                    'label' => 'Powtórz hasło',
                ],
                'constraints' => [
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'task_item',
        ]);
    }
}
