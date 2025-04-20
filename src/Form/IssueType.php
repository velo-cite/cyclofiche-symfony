<?php

namespace App\Form;

use App\Entity\IssueCategory;
use App\Model\Issue\IssueCreated;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => IssueCategory::class,
                'choice_label' => 'libelle',
            ])
            ->add('location')
            ->add('city')
            ->add('address')
            ->add('description', TextareaType::class, [])
        ;

        if (null === $this->security->getUser()) {
            $builder
                ->add('firstname')
                ->add('lastname')
                ->add('email', EmailType::class)
                ->add('phone')
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IssueCreated::class,
        ]);
    }
}
