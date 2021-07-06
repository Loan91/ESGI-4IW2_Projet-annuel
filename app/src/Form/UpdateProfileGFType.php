<?php

namespace App\Form;

use App\Entity\ProfilePicture;
use App\Entity\User;
use App\Validator\Phone;
use App\Validator\ValidPassword;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UpdateProfileGFType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'M.' => 'Monsieur',
                    'Mme./Mlle.' => 'Madame'
                ],
                'empty_data' => '',
                'expanded' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "La civilité ne peut pas être vide."
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le mot de passe ne peut pas être vide."
                    ]),
                    new Assert\Length([
                        'max' => 80,
                        'maxMessage' => "Le prénom ne peut excéder 80 caractères"
                    ]),
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le mot de passe ne peut pas être vide."
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => "Le nom de famille ne peut excéder 100 caractères"
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' =>  'Adresse email',
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
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le numéro de téléphone ne peut pas être vide."
                    ]),
                    new Phone()
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mot de passe doivent correspondre.',
                'options' => ['attr' => ['class' => '']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe', 'attr' => ['class' => 'mb-2']],
                'second_options' => ['label' => 'Répéter le mot de passe'],
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
