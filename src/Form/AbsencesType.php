<?php

namespace App\Form;

use App\Entity\Absences;
use App\Entity\TypesAbsences;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class AbsencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text',
            ])
            ->add('raison')
            ->add('document', FileType::class, array('data_class' => null, 'required' => false, 'label' => false, 'empty_data' => 'ok'))
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])

            ->add('type', EntityType::class, [
                'class' => TypesAbsences::class,
                'label' => false,
                'attr' => ['class' => 'attached-input'],
                'placeholder' => 'Aucun type d`absence',
                'required' => false,
            ])
            
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absences::class,
        ]);
    }
}
