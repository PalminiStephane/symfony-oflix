<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Votre E-Mail"
            ])
            ->add('roles', ChoiceType::class,  [
                "choices" => [
                    "ADMIN" => "ROLE_ADMIN",
                    "MANAGER" => "ROLE_MANAGER", 
                    "USER" => "ROLE_USER"
                ], 
                'multiple' => true,
                'expanded' => true
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                // dans event on récupère le formulaire (pour le modifier)
                // et aussi la donnée associé au formulaire
                // dd($event);
                $formulaire = $event->getForm();
                $data = $event->getData();
                
                // uniquement si la donnée associée (User) a un ID, càd vient de la BDD
                if ($data->getId() !== null)
                {
                    // j'ajoute cette partie uniquement si la donnée associée (User) a un ID, càd vient de la BDD
                    $formulaire->add('password', PasswordType::class, [
                        "attr" => [
                            "placeholder" => "laisse vide si inchangé"
                        ],
                        "mapped" => false
                    ]);
                } else {
                    // Sinon, (l'ID == null) alors on affiche la version pour créer l'utilisateur
                    $formulaire->add('password', PasswordType::class, [
                        "attr" => [
                            "placeholder" => "votre mot de passe"
                        ],
                        "mapped" => false,
                        'constraints' => [
                            new NotBlank(),
                            new Regex(
                                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                                "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                            ),
                        ]
                    ])
                    ->add('password_confirmed', PasswordType::class, [
                        "attr" => [
                            "placeholder" => "veuillez confirmer le mot de passe"
                        ],
                        "mapped" => false,
                        'constraints' => [
                            new NotBlank(),
                            new Regex(
                                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                                "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                            ),
                        ]
                        ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
