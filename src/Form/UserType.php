<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Equipe;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'attached-input'],
                'label' => false,
                'required' => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Directeur RH' => 'ROLE_DIRECTEURRH',
                    'Responsable Hiérarchique' => 'ROLE_RESPONSABLE_HIERA',
                    'Référent Frais' => 'ROLE_REFERENT_FRAIS',
                    'RTT' => 'ROLE_RTT',
                    'Collaborateur' => 'ROLE_COLLABORATEUR',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'data' => ['ROLE_USER'],
                'required' => true
            ])

            ->add('image', FileType::class, [
                'data_class' => null,
                'required' => false,
                'label' => false,
                'empty_data' => null
            ])
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'attached-input'],
                'label' => false,
                'required' => false
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['class' => 'attached-input'],
                'label' => false,
                'required' => false
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',  // Utilise un champ de texte unique avec un sélecteur de date
                'label' => false,           // Pas d'étiquette pour ce champ
                'required' => false,        // Champ non obligatoire
                'attr' => [
                    'class' => 'js-datepicker'  // Classe pour appliquer des styles ou des comportements JavaScript
                ]
            ])
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
