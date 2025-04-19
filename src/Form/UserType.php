<?php

namespace App\Form;

use App\Entity\User;
use App\Model\UserCreated;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserCreated::class,
        ]);
    }
}
