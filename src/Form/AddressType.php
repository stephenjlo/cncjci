<?php
namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line1', TextType::class, [
                'label' => 'Adresse ligne 1',
                'required' => false,
                'attr' => ['class' => 'form-control address-autocomplete'],
            ])
            ->add('line2', TextType::class, [
                'label' => 'Adresse ligne 2',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'data' => 'Côte d\'Ivoire',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lat', HiddenType::class, [
                'attr' => ['class' => 'address-lat'],
            ])
            ->add('lng', HiddenType::class, [
                'attr' => ['class' => 'address-lng'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}