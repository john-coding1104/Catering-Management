<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Supplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => function (Supplier $supplier) {
                    return $supplier->getName();
                },
                'placeholder' => '-- Select a supplier --',
                'attr' => ['class' => 'supplier-select']
            ])
            ->add('name', null, [
                'label' => 'Product Name',
            ])
            ->add('category')
            ->add('unitPrice', MoneyType::class, [
                'label' => 'Unit Price (₱)',
                'currency' => 'PHP',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 0.01,
                    'placeholder' => '0.00'
                ]
            ])
            ->add('createDate')
            ->add('imagePath', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'file-input', 'accept' => 'image/*']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
