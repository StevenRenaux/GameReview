<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreateAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label'=>'Nom d\'utilisateur',
                'constraints'=> [
                    new NotBlank([
                        'message'=>'Ce champ ne doit pas être vide'
                    ]),
                ],
            ])
            ->add('email', null,[
                'label'=>'Email',
                'constraints'=> [
                    new NotBlank([
                    'message'=>'Ce champ ne doit pas être vide',
                ]),
                    new Email([
                    'message'=>'L\'email n\'est pas valide'
                ]),
                ]
            ])

            ->add('password', RepeatedType::class,[
                'type'=>PasswordType::class,
                'required'=>false,// pour le edit mais nous en avons besoin pour le create user
                'help'=>'Votre mot de passe doit être compris entre 8 et 15 caractères et doit contenir au moins une minuscle,
                une majuscule, un chiffre et un des caractères spéciaux $ @ % * + - _ !',
                'mapped'=>false,
                'first_options'=>[
                    'label'=>'Mot de passe'
                ],
                'second_options'=>[
                    'label'=>'Retapez le mot de passe'
                ],
                'constraints'=> [
                    new NotBlank([
                    'allowNull'=>true,
                    'normalizer'=>'trim',
                    'message'=>'Ce champ ne doit pas être vide',
                    ]),
                    new Regex([
                        'pattern'=>'/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/',
                        'message' => 'Votre mot de passe doit être compris entre 8 et 15 caractères et doit contenir au moins une minuscle,
                         une majuscule, un chiffre et un des caractères spéciaux $ @ % * + - _ !',
                    ])
                ],
            ])

            // On utilise des FormEvents (événements de formulaire
            // pour modifier le formulaire en fonction du contexte)
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // dans $event on a le formaulaire et les données
                $form = $event->getForm();
                $user = $event->getData();

                // un id null veut dire que l'on crée un user
                // On souhaite ajouter un champs pour accepter les CGU
                if($user->getId() === null){
                    // On souhaite ajouter un champs pour accepter les CGU
                    $form->add('cgu', CheckboxType::class, [
                        'label'=>'j\'accepte les CGU',
                        'required'=>true,
                        'mapped'=>false,
                    ]);
                    // On souhaite que les champs du mot de passe soit requis lors d'ajout d'un user
                    // Il n'est pas possible de modifier un champ existant, on ne peut que le supprimer et l'ajouter à nouveau
                    $form->remove('password');
                    $form->add('password', RepeatedType::class,[
                        'type'=>PasswordType::class,
                        'help'=>'Votre mot de passe doit être compris entre 8 et 15 caractères et doit contenir au moins une minuscle,
                        une majuscule, un chiffre et un des caractères spéciaux $ @ % * + - _ !',
                        'first_options'=>[
                            'label'=>'Mot de passe'
                        ],
                        'second_options'=>[
                            'label'=>'Retapez le mot de passe'
                        ],
                        'required'=>'true',
                        'constraints'=> [
                            new NotBlank([
                            'normalizer'=>'trim',
                            'message'=>'Ce champ ne doit pas être vide',
                            ]),
                            new Regex([
                                'pattern'=>'/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/',
                                'message' => 'Votre mot de passe doit être compris entre 8 et 15 caractères et doit contenir au moins une minuscle,
                                 une majuscule, un chiffre et un des caractères spéciaux $ @ % * + - _ !',
                            ])
                        ],
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
