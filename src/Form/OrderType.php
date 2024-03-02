<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookOrder;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bookOrders', CollectionType::class, [
                'label' => false,
                'entry_options' => array('label' => false),
                'entry_type' => BookOrderType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'by_reference' => false
            ])
            ->add('customerName')
            ->add('customerPhone')
            ->add('customerAddress')
            ->add('totalMoney')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'multiple' => true,
            'csrf_protection' => false
        ]);
    }
}
