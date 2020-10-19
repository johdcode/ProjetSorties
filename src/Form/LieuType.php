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
            ->add('nom', EntityType::class, [
                'class' => Lieu::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.nom', 'ASC');
                },
                'label' => 'Lieu : ',
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
            ->add('ville', TextType::class, [
                'label' => 'Ville : ',
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add('Ajouter', ButtonType::class, [
                'attr' => ['id' => 'btn_ajouter_lieu']
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
