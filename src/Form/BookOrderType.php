<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookOrder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('book', EntityType::class, array(
                'class'     => Book::class,
                'expanded'  => true,
                'by_reference' => false,
                'choice_value' => 'id',
            ))
            ->add('quantity')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookOrder::class,
            'multiple' => true,
            'csrf_protection' => false
        ]);
    }
}
