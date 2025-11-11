<?php
namespace App\Form;

use App\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'choices' => [
                    'Standard' => 'Standard',
                    'Bureau' => 'Bureau',
                    'Mobile' => 'Mobile',
                    'Fax' => 'Fax',
                    'Urgence' => 'Urgence',
                ],
                'attr' => [
                    'class' => 'form-select form-select-sm',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un type']),
                ],
            ])
            ->add('number', TelType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => '+225 XX XX XX XX'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro est obligatoire']),
                ],
            ])
            ->add('isPrimary', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'data' => false,
                'attr' => [
                    'class' => 'd-none',
                ],
            ])
            ->add('position', HiddenType::class, [
                'data' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Phone::class,
        ]);
    }
}