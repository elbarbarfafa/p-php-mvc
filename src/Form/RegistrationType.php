<?php
namespace App\Form;

use App\Document\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Nom d\'utilisateur','attr' => [ "class" => 'form-control']])
            ->add('nom_client', TextType::class, ['label' => 'Nom complet','attr' => [ "class" => 'form-control']])
            ->add('adresse_client', TextType::class, ['label' => 'Adresse complète','attr' => [ "class" => 'form-control']])
            ->add('email', EmailType::class, ['label' => 'Email','attr' => [ "class" => 'form-control']])
            ->add('numero', type:TelType::class, options: ['label' => 'Numéro de téléphone','attr' => [ "class" => 'form-control']])
            ->add('password', RepeatedType::class, options: [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe','attr' => [ "class" => 'form-control']],
                'second_options' => ['label' => 'Répéter mot de passe','attr' => [ "class" => 'form-control']],
            ])
            ->add('save', SubmitType::class, ['label' => 'S\'inscrire','attr' => [ "class" => 'btn btn-primary']]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
