<?php

namespace AppBundle\Form;


use AppBundle\Entity\Hall;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HallType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('capacity', IntegerType::class, [
                'label' => 'Вместимость зала',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }
}