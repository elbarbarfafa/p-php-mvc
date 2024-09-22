<?php 
// src/Form/ChambreType.php
namespace App\Form;

use App\Document\Chambre;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('etage', IntegerType::class, ['label' => 'Étage', 'attr' => [ "class" => 'form-control']])
        ->add('type', TextType::class, ['label' => 'Type', 'attr' => [ "class" => 'form-control']])
        ->add('nombre_lit', IntegerType::class, ['label' => 'Nombre de lits','attr' => [ "class" => 'form-control']])
            ->add('hotel', DocumentType::class, [
                'class' => 'App\Document\Hotel',
                'choice_label' => 'nom_hotel',
                'label' => 'Hôtel',
                'attr' => [ "class" => 'form-control']
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer',
        'attr' => [ "class" => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
