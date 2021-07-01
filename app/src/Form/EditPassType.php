<?php

namespace App\Form;

use App\Validator\ValidPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class EditPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new UserPassword([
                        'message' => 'L\'ancien mot de passe est incorrect',
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mot de passe doivent correspondre.',
                'options' => ['attr' => ['class' => '']],
                'required' => true,
                'first_options'  => ['label' => 'Nouveau mot-de-passe', 'attr' => ['class' => 'mb-2']],
                'second_options' => ['label' => 'Répéter le mot-de-passe'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le mot de passe ne peut pas être vide."
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'minMessage' => "Le nom de famille ne peut excéder 100 caractères"
                    ]),
                    new ValidPassword(['minSize' => 8, 'maxSize' => 20])
                ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'method' => 'PATCH'
        ]);
    }
}
