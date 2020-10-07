<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Length;

class UserEditType extends AbstractType
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
        ]);
    }
}
