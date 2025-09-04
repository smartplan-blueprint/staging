<?php

namespace App\Form\SuperAdmin;

use App\Entity\Merchant\MerchantDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchantDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupName', TextType::class, [
                'label' => 'Group Name',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'empty_data' => ''
            ])
            ->add('contactName', TextType::class, [
                'label' => 'Contact Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Contact Email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('contactPhone', TelType::class, [
                'label' => 'Contact Phone',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MerchantDetails::class,
        ]);
    }
}
