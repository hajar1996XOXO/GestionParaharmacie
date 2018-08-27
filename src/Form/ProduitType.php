<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('categorie',EntityType::class,[
                'class'=> Categorie::class,
                'choice_label'=>'titre'
            ])
            ->add('fournisseur',EntityType::class,[
                'class'=> Fournisseur::class,
                'choice_label'=>'nom'
            ])
            ->add('qteTotale')
            ->add('dateExp')
            ->add('marque')
            ->add('prix')
            ->add('dateLivraison')
            ->add('imagePath', FileType::class, array('label' => 'Image(JPG)','data_class' => null,'required' => false))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
