<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'label' => 'Lieu : '
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue : '
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude : ',
                'required' => false
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude : ',
                'required' => false
            ])
            ->add('ville', FormType::class)
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->orderBy('v.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'label' => 'Ville : '
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
