<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Services;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name',
            ])
            ->add('customerName', TextType::class)
            ->add('eventDate', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'type' => 'date',
                    'required' => true,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select a booking date.'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The booking date must be today or a future date. Past dates are not allowed.'
                    ]),
                ],
                'help' => 'Please select today or a future date.',
                'help_attr' => ['class' => 'text-sm text-gray-600 mt-1'],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'pending',
                    'Confirmed' => 'confirmed',
                    'Completed' => 'completed',
                    'Cancelled' => 'cancelled',
                ],
            ])
            ->add('guestCount', IntegerType::class)
            ->add('totalPrice', MoneyType::class, [
                'currency' => 'PHP',
                'divisor' => 1,
                'attr' => [
                    'readonly' => true,
                    'id' => 'total-price'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'isEdit' => false, // Default to create mode
        ]);
    }
}


