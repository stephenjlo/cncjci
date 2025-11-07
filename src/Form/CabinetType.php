<?php
namespace App\Form;

use App\Entity\Cabinet;
use App\Entity\CabinetType as CabinetTypeEntity;
use App\Entity\Lawyer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class CabinetType extends AbstractType
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Cabinet|null $cabinet */
        $cabinet = $options['data'] ?? null;

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du cabinet',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL)',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help' => '<small class="form-text text-info">Laissez vide pour génération automatique à partir du nom</small>',
                'help_html' => true,
            ])
            ->add('typeEntity', EntityType::class, [
                'class' => CabinetTypeEntity::class,
                'choice_label' => 'name',
                'label' => 'Type de cabinet',
                'required' => false,
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionner un type',
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'Logo du cabinet',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF, WebP)',
                    ])
                ],
                'help' => $cabinet && $cabinet->getLogoUrl() ? 'Logo actuel : ' . basename($cabinet->getLogoUrl()) : 'Formats acceptés : JPG, PNG, GIF, WebP (max 2Mo)',
            ])
        ;

        // Responsable et avocats - seulement à la création (pas en modification)
        // En modification, ces champs seront gérés via une page dédiée
        $isCreation = !($cabinet && $cabinet->getId());

        if ($isCreation) {
            // Responsable du cabinet - tous les avocats disponibles
            $builder->add('managingPartner', EntityType::class, [
                'class' => Lawyer::class,
                'choice_label' => function(Lawyer $lawyer) {
                    return $lawyer->getFullName() . ($lawyer->getCabinet() ? ' (' . $lawyer->getCabinet()->getName() . ')' : ' (Sans cabinet)');
                },
                'label' => 'Associé gérant / Responsable',
                'required' => false,
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionner le responsable (optionnel)',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('l')
                        ->orderBy('l.lastName', 'ASC');
                },
                'help' => '<small class="form-text text-info">Vous pouvez désigner un avocat comme responsable. Les avocats doivent être créés au préalable.</small>',
                'help_html' => true,
            ]);

            // Avocats à rattacher - tous les avocats disponibles (avec Select2)
            $builder->add('lawyers', EntityType::class, [
                'class' => Lawyer::class,
                'choice_label' => function(Lawyer $lawyer) {
                    return $lawyer->getFullName() . ($lawyer->getCabinet() ? ' (' . $lawyer->getCabinet()->getName() . ')' : ' (Sans cabinet)');
                },
                'label' => 'Avocats à rattacher',
                'required' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-select select2-lawyers', 'data-placeholder' => 'Rechercher et sélectionner des avocats...'],
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('l')
                        ->orderBy('l.lastName', 'ASC');
                },
                'help' => '<small class="form-text text-info">Recherchez et sélectionnez les avocats à rattacher (multi-sélection). Les avocats doivent être créés au préalable.</small>',
                'help_html' => true,
            ]);
        }

        $builder
            ->add('address', AddressType::class, [
                'label' => false,
                'required' => false,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cabinet::class,
        ]);
    }
}
