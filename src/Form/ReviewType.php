<?php

namespace App\Form;

use App\Entity\Review;
use App\Form\GameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('game', GameType::class, [
                'label'=>false,
            ])
            ->add('title', null, [
                'label'=>'Titre de la review',
                'help'=>'Le titre de la review doit être compris en 2 et 100 caractères',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Ce champ ne peut être vide',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le titre doit être composé au minimum de {{ limit }} caractères',
                        'maxMessage' => 'Le titre doit être composé au maximum de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('content', null, [
                'label'=>'Contenu de la review',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Ce champ ne peut être vide',
                    ]),
                ],
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
