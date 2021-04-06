<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('area', IntegerType::class, [
                'label' => 'Aire en m²',
                'empty_data' => 0
            ])
            ->add('description', null, [
                // 'help' => 'Décrivez votre bien',
                'empty_data' => ''
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
            ->add('isFurnished', null, [
                'label' => 'Le bien est-il meublé?',
                'empty_data' => false
            ])
            ->add('containsStorage', null, [
                'label' => 'Est-ce que le bien possède des éléments de stockage',
                'empty_data' => false
            ])
            ->add('isKitchenSeparated', null, [
                'label' => 'Est-ce que la cuisine est séparée de la salle à manger ?',
                'empty_data' => false
            ])
            ->add('containDiningRoom', null, [
                'label' => 'Le bien possède t-il une salle à manger?',
                'empty_data' => false
            ])
            ->add('ground', null, [
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
            ->add('fireplace', null, [
                'label' => 'Y a-t-il une cheminée?',
                'empty_data' => false
            ])
            ->add('elevator', null, [
                'label' => 'Y a-t-il un assenseur?',
                'empty_data' => false
            ])
            ->add('externalStorage', null, [
                'label' => 'Y a-t-il une pièce de stockage externe?',
                'empty_data' => false
            ])
            ->add('areaExternalStorage', null, [
                'required' => false,
                'label' => 'Quelle est la taille de cet espace? (si stockage externe)',
                'empty_data' => 0
            ])
            ->add('guarding', null, [
                'label' => 'Y a-t-il un système de sécurité?',
                'empty_data' => false
            ])
            ->add('energyConsumption', IntegerType::class, [
                'label' => 'Combien d\'énergie consomme le bien ?',
                'empty_data' => 0
            ])
            ->add('gasEmissions', IntegerType::class, [
                'label' => 'Combien de gaz à émission ce bien émet?',
                'empty_data' => 0
            ])
            ->add('address', null, [
                'label' => 'Adresse du bien',
                'empty_data' => ''
            ])
            ->add('zipCode', null, [
                'label' => 'Code postal',
                // 'help' => 'Le code postal lié à votre adresse',
                'empty_data' => ''
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'empty_data' => ''
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
                'empty_data' => 0,
                'currency' => ''
            ])
            ->add('charges', MoneyType::class, [
                'label' => 'Prix des charges du bien',
                'empty_data' => 0,
                'currency' => ''
            ])
            ->add('guarentee', MoneyType::class, [
                'label' => 'Prix de la garantie du loyer',
                'empty_data' => 0,
                'currency' => ''
            ])
            ->add('feesPrice', MoneyType::class, [
                'label' => 'Prix des honoraires',
                'empty_data' => 0,
                'currency' => ''
            ])
            ->add('inventoryPrice', MoneyType::class, [
                'label' => 'Prix d\'une visite',
                'empty_data' => 0,
                'currency' => ''
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
