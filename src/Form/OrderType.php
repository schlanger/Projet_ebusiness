<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Transporter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options['user']);
        $builder
            ->add('adresses',EntityType::class,[
                'class' => Adress::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'choices' => $options['user']->getAdresses(),
                'expanded' => true ,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une adresse de livraison',
                    ]),
                ],

            ])
            ->add('transporter',EntityType::class,[
                'class'=> Transporter::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true ,
            ])
            ->add('payment',ChoiceType::class,[
                'choices' => [
                'Payer par Paypal' => 'paypal',
                'Payer par Stripe' => 'stripe',
                ],
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true ,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'user' => [],
        ]);
    }
}
