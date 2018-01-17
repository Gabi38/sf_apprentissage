<?php

namespace GalerieBundle\Form;

use GalerieBundle\Entity\Galerie;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GalerieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder
		    ->add('title', TextType::class)
		    ->add('categorie', EntityType::class, array(
				    'class' => 'GalerieBundle:Categorie',
				    'choice_label' => 'nom',
				    'placeholder' => 'Choisir une catégorie'
			    )
		    )
		    ->add('images', CollectionType::class, array(
			    'entry_type'   => ImageType::class,
			    'allow_add'    => true,
			    'allow_delete' => true,
			    'label' => ' '
		    ))
		    ->add('Enregistrer', SubmitType::class);



    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GalerieBundle\Entity\Galerie'// Galerie::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'galeriebundle_galerie';
    }


}
