<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('roles')
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',

                    'constraints' => [
//                        new NotBlank([
//                            'message' => 'Entrez un mot de passe',
//                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le mot de passe doit être d\'au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                    'mapped' => false,
                    'constraints' => [
//                        new NotBlank([
//                            'message' => 'Entrez un mot de passe',
//                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'La confirmation de mot de passe doit être d\'au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
            ])
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('urlPhoto')
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'placeholder' => 'Un pseudo',
                    'class' => 'form-control'
                ]
            ])
            ->add('administrateur')
            ->add('actif')
//            ->add('campus')
        ;
//        dd($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
