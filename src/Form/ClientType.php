<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('password',PasswordType::class)
            ->add('confirmPassword',PasswordType::class)
            ->add('adressLivraison')
            ->add('villeLivraison')
            ->add('civility',ChoiceType::class, [
                'choices'  => [
                    'Monsieur' => 'Monsieur',
                    'Madame' => 'Madame',
                    'Mademoiselle' =>'Mademoiselle',
                ]])
            ->add('tel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
