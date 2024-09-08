<?php

namespace App\Form;

use App\Entity\Deplacement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class DeplacementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('adresseDepart', TextType::class, [
            'attr' => ['id' => 'adresse-depart-cache'],
            'required' => false,
        ])
        ->add('adresseArrive', TextType::class, [
            'attr' => ['id' => 'adresse-arrive-cache'],
            'required' => false,
        ])

        ->add('raison',TextType::class, [
            'attr' => [
                'class' => 'attached-input',
                'placeholder' => 'Introduction'
            ],
            'label' => false,
            'required' => false
        ])

            ->add('distance')
            ->add('cout')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Non' => 'Non', 
                    'Attente' => 'Attente', 
                    'Oui' => 'Oui',
                ],
                'label' => false,
                'required' => false,
                'placeholder' => '-- Status actuel -- ', // Assurez-vous que c'est clair pour l'utilisateur
    
                'attr' => [
                    'class' => 'attached-input',
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deplacement::class,
        ]);
    }
}

