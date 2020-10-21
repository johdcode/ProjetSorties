<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville : '
            ])
            ->add('ajouter', ButtonType::class, [
                'attr' => [
                    'type' => 'button',
                    'class' => 'btn btn-success',
                    'data-toggle' => 'modal',
                    'data-target' => '#ajouterLieu'
                ]
            ])
            ->add('rue', TextType::class, [
                'disabled' => 'true',
                'label' => 'Rue : ',
                'required' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add('latitude', NumberType::class, [
                'disabled' => 'true',
                'label' => 'Latitude : ',
                'required' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add('longitude', NumberType::class, [
                'disabled' => 'true',
                'label' => 'Longitude : ',
                'required' => false,
                'attr' => [
                    'disabled' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
