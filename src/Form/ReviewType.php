<?php

namespace App\Form;

use App\Entity\Review;
use App\Form\GameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('game', GameType::class, [
                'label'=>false,
            ])
            ->add('title', null, [
                'label'=>'Titre de la review'
            ])
            ->add('content', null, [
                'label'=>'Contenu de la review'
            ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
