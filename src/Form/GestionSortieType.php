<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GestionSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'required' => false,
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tout les campus',
                'query_builder' => function (EntityRepository  $er) {
                return $er->createQueryBuilder('c')
                    ->addOrderBy('c.nom', 'ASC');
                }
            ])
            ->add('nom',TextType::class, ['label' => 'Le nom de la sortie contient : ', 'required' => false])
            ->add('borneDateMin',DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Entre ',
                'data' => \DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00')
            ])
            ->add('borneDateMax',DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'et ',
                'data' => new \DateTime('NOW')
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'mapped' => false,
                'required' => false
            ])
            ->add('etatInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'mapped' => false,
                'required' => false
            ])
            ->add('etatPasInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'mapped' => false,
                'required' => false
            ])
            ->add('etatPasse', CheckboxType::class,[
                'label' => 'Sorties passées',
                'mapped' => false,
                'required' => false
            ])
            ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefaults([
//            'data_class' => Sortie::class,
//        ]);

    }
}
