<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangePasswordType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('method', Request::METHOD_PUT);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => ' ',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 8),
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control mb-2',
                    'required' => 'required',
                    'autofocus' => 'autofocus',
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => ' ',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 8),
                    new Callback(function (mixed $value, ExecutionContextInterface $context) {
                        $password = $context->getRoot()->getData()->getPassword();
                        if ($password !== $value) {
                            $context->addViolation('The passwords do not match.');
                        }
                    })
                ],
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