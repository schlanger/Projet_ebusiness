<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdressType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options['user']);
        $builder
            ->add('title', TextType::class, [
                'label' => 'Intitulé de l adresse',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir l\'intitulé de l\'adresse.',
                    ]),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'L\'intitulé de l\'adresse doit comporter au moins {{ limit }} caractères.', 'maxMessage' => 'L\'intitulé de l\'adresse ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'prénom',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un prénom.',
                    ]),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'Le prénom doit comporter au moins {{ limit }} caractères.', 'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom.',
                    ]),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.', 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('company',TextType::class,[
                'label' => 'société',
                'required' => false,
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse.',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'L\'adresse doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('postalcode',TextType::class,[
                'label' => 'Code postal',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le code postal',
                    ]),
                    new Length(['min' => 5, 'max' => 5, 'exactMessage' => 'Le code postal doit comporter {{ limit }} caractères.']),
                ],
            ])
            ->add('country',TextType::class,[
                'label' => 'Pays',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le pays.',
                    ]),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'Le pays doit comporter au moins {{ limit }} caractères.', 'maxMessage' => 'Le pays ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('phone',TextType::class,[
                'label' => 'Téléphone',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le numéro de téléphone.',
                    ]),
                    new Length(['min' => 10, 'max' => 10, 'exactMessage' => 'Le numéro de téléphone doit comporter {{ limit }} caractères.']),
                ],
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la ville.',
                    ]),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'La ville doit comporter au moins {{ limit }} caractères.', 'maxMessage' => 'La ville ne peut pas dépasser {{ limit }} caractères.']),
                ],

            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Adress::class,
        ]);
    }
}