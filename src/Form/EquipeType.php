<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\User;
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

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom',TextType::class, [
                'attr' => [
                    'class' => 'attached-input',
                    'placeholder' => 'Nom équipe'
                ],
                'label' => false,
                'required' => false
            ])

            ->add('responsable', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getPrenom() . ' ' . $user->getNom(); // Concaténer le prénom et le nom
                },
                'attr' => [
                    'class' => 'attached-input',
                    'placeholder' => 'Sélectionner le responsable'
                ],
                'label' => false,
                'required' => false
            ])
            
            ->add('parent', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => '-- Choisir une équipe parente --',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nom', 'ASC');
                },
                'empty_data' => null,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
