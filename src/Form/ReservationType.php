<?php
namespace App\Form;

use App\Document\Chambre;
use App\Document\Reservation;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType ;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hotel', DocumentType::class, [
                'class' => 'App\Document\Hotel',
                'choice_label' => 'nom_hotel',
                'label' => 'Hotel','attr' => [ "class" => 'form-control']
            ])
            ->add('client', DocumentType::class, [
                'class' => 'App\Document\Client',
                'choice_label' => 'nom_client',
                'label' => 'Client','attr' => [ "class" => 'form-control']
            ])
            ->add('chambres', DocumentType::class, [
                'class' => 'App\Document\Chambre',
                'choice_label' => function(Chambre $chambre){
                    return " Type: $chambre->type, Lits: $chambre->nombre_lit, Etage: $chambre->etage";
                },
                'label' => 'Chambres',
                'multiple'=>true,
                'expanded' => true,
                'attr' => [ "class" => 'form-control']
            ])
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début','attr' => [ "class" => 'form-control']
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin','attr' => [ "class" => 'form-control']
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer','attr' => [ "class" => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'chambres' => [],
        ]);
    }
}
