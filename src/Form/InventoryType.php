<?php

namespace App\Form;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Entity\Supplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Product',
                'placeholder' => 'Choose a product',
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#235A2F]'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Enter item description (optional)'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Food & Beverages' => 'Food & Beverages',
                    'Kitchen Equipment' => 'Kitchen Equipment',
                    'Tableware' => 'Tableware',
                    'Linens & Decorations' => 'Linens & Decorations',
                    'Cleaning Supplies' => 'Cleaning Supplies',
                    'Office Supplies' => 'Office Supplies',
                    'Maintenance' => 'Maintenance',
                    'Other' => 'Other'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('currentStock', IntegerType::class, [
                'label' => 'Current Stock',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'placeholder' => 'Enter current stock quantity'
                ]
            ])
            ->add('minimumStock', IntegerType::class, [
                'label' => 'Minimum Stock Level',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'placeholder' => 'Enter minimum stock level'
                ]
            ])
            ->add('maximumStock', IntegerType::class, [
                'label' => 'Maximum Stock Level',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Enter maximum stock level'
                ]
            ])
            ->add('unitPrice', MoneyType::class, [
                'label' => 'Unit Price (₱)',
                'currency' => 'PHP',
                'required' => false,
                'attr' => [
                    'class' => 'form-control unit-price-input',
                    'min' => 0,
                    'step' => 0.01,
                    'placeholder' => '0.00',
                    'data-autofill' => 'unit-price'
                ]
            ])
            ->add('unit', ChoiceType::class, [
                'label' => 'Unit of Measurement',
                'choices' => [
                    'Piece' => 'piece',
                    'Kilogram' => 'kg',
                    'Gram' => 'g',
                    'Liter' => 'L',
                    'Milliliter' => 'mL',
                    'Box' => 'box',
                    'Pack' => 'pack',
                    'Set' => 'set',
                    'Dozen' => 'dozen',
                    'Meter' => 'm',
                    'Centimeter' => 'cm',
                    'Other' => 'other'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'name',
                'label' => 'Supplier',
                'required' => false,
                'placeholder' => 'Choose a supplier (optional)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('supplierContact', TextType::class, [
                'label' => 'Supplier Contact',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter supplier contact (optional)'
                ]
            ])
            ->add('lastRestocked', DateType::class, [
                'label' => 'Current Stock Date',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Active' => 'active',
                    'Inactive' => 'inactive',
                    'Discontinued' => 'discontinued'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Item Image',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ]
            ]);

        // Auto-populate name from product on submit
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (isset($data['product']) && empty($data['name'])) {
                // Name will be set from product in the controller
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
        ]);
    }
}
