<?php

// src/Form/HotelType.php
namespace App\Form;

use App\Document\Hotel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_hotel', TextType::class, ['label' => 'Nom','attr' => [ "class" => 'form-control']])
            ->add('adresse_hotel', TextType::class, ['label' => 'Adresse','attr' => [ "class" => 'form-control']])
            ->add('categorie_hotel', TextType::class, ['label' => 'Catégorie','attr' => [ "class" => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer','attr' => [ "class" => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hotel::class,
        ]);
    }
}
