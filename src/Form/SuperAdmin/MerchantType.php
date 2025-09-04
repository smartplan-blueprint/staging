<?php

namespace App\Form\SuperAdmin;


use App\Entity\Merchant;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Merchant Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('code', TextType::class, [
                'label' => 'Merchant Code',
                'attr' => ['class' => 'form-control']
            ])
            ->add('registrationDate', DateType::class, [
                'label' => 'Registration Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Is Active',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('clientId', TextType::class, [
                'label' => 'Client ID',
                'attr' => ['class' => 'form-control']
            ])
            ->add('merchantDetails', MerchantDetailsType::class, [
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Merchant::class,
        ]);
    }
}
