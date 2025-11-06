<?php
namespace App\Form;

use App\Entity\EmailAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType as SymfonyEmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Professionnel, Personnel, etc.'],
            ])
            ->add('email', SymfonyEmailType::class, [
                'attr' => ['placeholder' => 'email@example.com'],
            ])
            ->add('isPrimary', CheckboxType::class, [
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'data' => 0,
                'attr' => ['min' => 0],
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