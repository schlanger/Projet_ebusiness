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
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'prénom',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('company',TextType::class,[
                'label' => 'société',
                'required' => false,
            ])
            ->add('adress',TextType::class,[
                'label' => 'Adresse',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('postalcode',TextType::class,[
                'label' => 'Code postal',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 5, 'max' => 5]),
                ],
            ])
            ->add('country',TextType::class,[
                'label' => 'Pays',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('phone',TextType::class,[
                'label' => 'Téléphone',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 10, 'max' => 10]),
                ],
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 2, 'max' => 255]),
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