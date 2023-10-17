<?php

namespace App\Form;

use App\Entity\Currency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('method', Request::METHOD_PUT);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selectedCurrency', EntityType::class, [
                'class' => Currency::class,
                'label' => ' ',
                'choice_label' => 'code',
                'attr' => [
                    'class' => 'form-control'
                ],
            ]);
    }
}