<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Maison' => 'maison',
                    'Appartement' => 'appartement'
                ],
                'label' => 'Type de bien'
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Studio' => 'studio'
                ] + (function () {
                    $data = [];
                    for ($i = 0; $i < 15; $i++) {
                        $data['f' . $i] = 'f' . $i;
                    }
                    return $data;
                })()
            ])
            ->add('area', TextType::class, [
                'label' => 'Aire en m²',
                'empty_data' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => ['numeric'],
                        'message' => "La donnée {{ value }} n'est pas un nombre valide."
                    ])
                ]
            ])
            ->add('description', null, [
                // 'help' => 'Décrivez votre bien',
                'empty_data' => '',
                'required' => false
            ])
            ->add('constructionDate', null, [
                'label' => 'Date de construction',
                'widget' => 'single_text',
                'html5' => 'true',
                'empty_data' => ''
            ])
            ->add('floor', IntegerType::class, [
                'required' => false, // Si appartement
                'label' => 'A quel étage se trouve le bien?',
                'empty_data' => 0
            ])
            ->add('floors', IntegerType::class, [
                'label' => 'Nombre d\'étages',
                'data' => '1',
                'empty_data' => 0
            ])
            ->add('rooms', IntegerType::class, [
                'label' => 'Nombre de pièces',
                'data' => '1',
                'empty_data' => 0
            ])
            ->add('bedrooms', IntegerType::class, [
                'label' => 'Nombre de chambres',
                'data' => '1',
                'empty_data' => 0
            ])
            ->add('bathrooms', IntegerType::class, [
                'label' => 'Nombre de salles de bain/salles d\'eau',
                'data' => '1',
                'empty_data' => 0
            ])
            ->add('toilets', IntegerType::class, [
                'label' => 'Nombre de toilettes',
                'data' => '1',
                'empty_data' => 0
            ])
            ->add('isFurnished', CheckboxType::class, [
                'label' => 'Le bien est-il meublé?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('containsStorage', CheckboxType::class, [
                'label' => 'Est-ce que le bien possède des éléments de stockage',
                'empty_data' => false,
                'required' => false
            ])
            ->add('isKitchenSeparated', CheckboxType::class, [
                'label' => 'Est-ce que la cuisine est séparée de la salle à manger ?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('containDiningRoom', CheckboxType::class, [
                'label' => 'Le bien possède t-il une salle à manger?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('ground', TextType::class, [
                'label' => 'Quel est le type de sol? (bois, inox...)',
                'empty_data' => ''
            ])
            ->add('heater', ChoiceType::class, [
                'label' => 'Comment est installé le chauffage?',
                'choices' => [
                    'Individuel' => 'individuel',
                    'Collectif' => 'collectif'
                ]
            ])
            ->add('fireplace', CheckboxType::class, [
                'label' => 'Y a-t-il une cheminée?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('elevator', CheckboxType::class, [
                'label' => 'Y a-t-il un assenseur?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('externalStorage', CheckboxType::class, [
                'label' => 'Y a-t-il une pièce de stockage externe?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('areaExternalStorage', CheckboxType::class, [
                'label' => 'Quelle est la taille de cet espace? (si stockage externe)',
                'empty_data' => false,
                'required' => false
            ])
            ->add('guarding', CheckboxType::class, [
                'label' => 'Y a-t-il un système de sécurité?',
                'empty_data' => false,
                'required' => false
            ])
            ->add('energyConsumption', TextType::class, [
                'label' => 'Combien d\'énergie consomme le bien ?',
                'empty_data' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => ['numeric'],
                        'message' => "La donnée {{ value }} n'est pas un nombre valide."
                    ])
                ]
            ])
            ->add('gasEmissions', TextType::class, [
                'label' => 'Combien de gaz à émission ce bien émet?',
                'empty_data' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => ['numeric'],
                        'message' => "La donnée {{ value }} n'est pas un nombre valide."
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse du bien',
                'empty_data' => '',
                'constraints' => [
                    new Assert\Length([
                        'min' => 1,
                        'max' => 150,
                        'minMessage' => "L'adresse doit faire minimum {{ limit }} caractères.",
                        'maxMessage' => "L'adresse doit faire maximum {{ limit }} caractères.",
                    ]),
                ]
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                // 'help' => 'Le code postal lié à votre adresse',
                'empty_data' => '',
                'constraints' => [
                    new Assert\Length([
                        'min' => 5,
                        'max' => 5,
                        'exactMessage' => "Le code postal doit faire {{ limit }} caractères."
                    ]),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'empty_data' => '',
                'constraints' => [
                    new Assert\Length([
                        'min' => 1,
                        'max' => 100,
                        'minMessage' => "La ville doit faire minimum {{ limit }} caractères.",
                        'maxMessage' => "La ville doit faire maximum {{ limit }} caractères.",
                    ]),
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'data' => 'FR'
            ])
            ->add('rentOrSale', ChoiceType::class, [
                'label' => 'Est-ce une vente ou une location?',
                'choices' => [
                    'Vente' => 'vente',
                    'Location' => 'location'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du bien',
                'empty_data' => false,
                'currency' => ''
            ])
            ->add('charges', MoneyType::class, [
                'label' => 'Prix des charges du bien',
                'empty_data' => false,
                'currency' => ''
            ])
            ->add('guarentee', MoneyType::class, [
                'label' => 'Prix de la garantie du loyer',
                'empty_data' => false,
                'currency' => ''
            ])
            ->add('feesPrice', MoneyType::class, [
                'label' => 'Prix des honoraires',
                'empty_data' => false,
                'currency' => ''
            ])
            ->add('inventoryPrice', MoneyType::class, [
                'label' => 'Prix d\'une visite',
                'empty_data' => false,
                'currency' => ''
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Rendre publique cette habitation?',
                'empty_data' => false,
                'required' => false,
                'data' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
