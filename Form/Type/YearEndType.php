<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YearEndType extends AbstractType
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentYear = date('Y');

        $builder
            ->add(
                'year',
                ChoiceType::class,
                [
                    'label' => 'Year to Process',
                    'required' => true,
                    'choices' => array_combine(
                        range((int) $currentYear - 1, (int) $currentYear),
                        range((int) $currentYear - 1, (int) $currentYear)
                    ),
                    'data' => (int) $currentYear,
                ]
            )
            ->add(
                'confirmation',
                TextareaType::class,
                [
                    'label' => 'Confirmation Message',
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'readonly' => true,
                        'rows' => 5,
                    ],
                    'data' => 'WARNING: This will process the year-end for ' . $currentYear . '. This will:
1. Move any unpaid dues to arrears
2. Create fields for the next year
3. Set subscription amounts for members based on their subscription type
4. Reset the current paid status for all members

Please make sure you have created settings for year ' . ((int) $currentYear + 1) . ' before proceeding.',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lodgesubscription_yearend';
    }
} 