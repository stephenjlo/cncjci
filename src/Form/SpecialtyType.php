<?php
namespace App\Form;

use App\Entity\Specialty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SpecialtyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la spécialité',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Droit des affaires'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de la spécialité est obligatoire']),
                    new Assert\Length([
                        'max' => 120,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'droit-des-affaires'],
                'required' => false,
                'help' => 'Laissez vide pour générer automatiquement à partir du nom',
                'constraints' => [
                    new Assert\Length([
                        'max' => 120,
                        'maxMessage' => 'Le slug ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Description de la spécialité juridique...'],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Specialty::class,
        ]);
    }
}
