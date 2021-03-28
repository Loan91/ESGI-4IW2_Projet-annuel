<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\ValidPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'email',
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'email ne peut pas être vide."
                    ]),
                    new Assert\Email([
                        'message' => "L'email {{ value }} n'est pas une adresse email valide."
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'mot de passe',
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => "Le mot de passe est trop court. Entrez au minimum 6 caractères."
                    ]),
                    new Assert\NotBlank([
                        'message' => "Le mot de passe ne peut pas être vide."
                    ]),
                    new \App\Validator\ValidPassword([
                        'min' => 6
                    ])
                ]
            ])
            // ->add('password', RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     'empty_data' => '',
            //     'invalid_message' => 'The password fields must match.',
            //     'options' => ['attr' => ['class' => 'password-field']],
            //     'required' => true,
            //     'first_options'  => ['label' => 'mot de passe'],
            //     'second_options' => ['label' => 'répétrez le mode de passe'],
            //     'constraints' => [
            //         new Assert\Length([
            //             'min' => 6,
            //             'minMessage' => 'Le mot de passe est trop court. Entrez au minimum 6 caractères.'
            //         ]),
            //         new ValidPassword(['min' => 6])
            //     ],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
