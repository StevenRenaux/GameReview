<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label'=>'Titre',
                'help'=>'Le titre du commentaire doit être compris en 2 et 100 caractères',
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
                'label'=>'Commentaire',
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
            'data_class' => Comment::class,
        ]);
    }
}
