<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GestionSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository  $er) {
                return $er->createQueryBuilder('c')
                    ->addOrderBy('c.nom', 'ASC');
                }
            ])
            ->add('nom',null, ['label' => 'Le nom de la sortie contient : '])
            ->add('dateHeureDebut',DateType::class, ['label' => 'Entre '])
            ->add('dateLimiteInscription',DateType::class, ['label' => 'et '])
            ->add('organisateur', CheckboxType::class,[
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'mapped' => false,
            ])
            ->add('etatInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'mapped' => false,

            ])
            ->add('etatPasInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'mapped' => false,
            ])
            ->add('etatPasses', CheckboxType::class,[
                'label' => 'Sorties passées',
                'mapped' => false,
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
