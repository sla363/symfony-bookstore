<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control mb-2',
                    'required' => 'required',
                    'autofocus' => 'autofocus'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control mb-2',
                    'required' => 'required',
                    'autofocus' => 'autofocus',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 8),
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirm password',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control mb-2',
                    'required' => 'required',
                    'autofocus' => 'autofocus',
                ],
            ]);
    }

}