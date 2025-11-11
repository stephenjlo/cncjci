<?php
namespace App\Form;

use App\Entity\EmailAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType as SymfonyEmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmailAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'choices' => [
                    'Contact' => 'Contact',
                    'Professionnel' => 'Professionnel',
                    'Info' => 'Info',
                    'Support' => 'Support',
                    'RDV' => 'RDV',
                ],
                'attr' => [
                    'class' => 'form-select form-select-sm',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un type']),
                ],
            ])
            ->add('email', SymfonyEmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => 'contact@cabinet.ci'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                    new Email(['message' => 'Email invalide']),
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
            'data_class' => EmailAddress::class,
        ]);
    }
}