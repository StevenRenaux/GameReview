<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
            'label'=>'Titre du jeu',
            'help'=>'Le titre du jeu doit être compris en 2 et 100 caractères',
            'constraints'=>[
                new NotBlank([
                    'message'=>'Ce champ ne peut être vide',
                ]),
                new Length([
                    'min' => 2,
                    'max' => 100,
                    'minMessage' => 'Le titre doit être composé au minimum de {{ limit }} caractères',
                    'maxMessage' => 'Le titre doit être composé au maximum de {{ limit }} caractères',
                ])
            ],
            ])
            ->add('genre', null, [
                'placeholder'=>'Selectionner un genre',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Ce champ ne peut être vide',
                    ]),
                ],
            ])
            ->add('platform', null, [
                'label'=>'Console(s)',
                'help'=>'Selectionner au minimum une console',
                'expanded'=>true,
                'multiple'=>true,
                'constraints'=> [
                    new NotBlank([
                        'message'=>'Ce champ ne doit pas être vide'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
