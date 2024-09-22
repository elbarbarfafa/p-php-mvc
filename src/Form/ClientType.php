<?php 
// src/Form/ClientType.php
namespace App\Form;

use App\Document\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_client', TextType::class, ['label' => 'Nom','attr' => [ "class" => 'form-control']])
            ->add('email', EmailType::class, ['label' => 'Email','attr' => [ "class" => 'form-control']])
            ->add('adresse_client', TextType::class, ['label' => 'Adresse complète','attr' => [ "class" => 'form-control']])
            ->add('numero', TelType::class, ['label' => 'Numéro de téléphone','attr' => [ "class" => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer',
        'attr' => [ "class" => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
