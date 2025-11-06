<?php
namespace App\Form;

use App\Entity\Lawyer;
use App\Entity\Cabinet;
use App\Entity\Specialty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LawyerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $selfEdit = $options['self_edit'] ?? false;

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('barNumber', TextType::class, [
                'label' => 'Numéro au Barreau',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'disabled' => $selfEdit, // Lawyer ne peut pas modifier
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Biographie',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5],
            ])
            ->add('photoUrl', UrlType::class, [
                'label' => 'URL Photo',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
        ;

        // Cabinet: seulement pour SUPER_ADMIN ou RESPO_CABINET (pas en self_edit)
        if (!$selfEdit) {
            $cabinetOptions = [
                'class' => Cabinet::class,
                'choice_label' => 'name',
                'label' => 'Cabinet',
                'required' => false,
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionner un cabinet',
            ];

            // RESPO_CABINET ne peut assigner qu'à son cabinet
            if ($user && $user->isRespoCabinet() && !$user->isSuperAdmin()) {
                $cabinetOptions['data'] = $user->getCabinet();
                $cabinetOptions['disabled'] = true;
            }

            $builder->add('cabinet', EntityType::class, $cabinetOptions);
        }

        $builder
            ->add('specialties', EntityType::class, [
                'class' => Specialty::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Spécialités',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('phones', CollectionType::class, [
                'entry_type' => PhoneType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('emails', CollectionType::class, [
                'entry_type' => EmailAddressType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('address', AddressType::class, [
                'label' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lawyer::class,
            'user' => null,
            'self_edit' => false,
        ]);
    }
}