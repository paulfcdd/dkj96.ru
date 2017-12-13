<?php

namespace AppBundle\Form;


use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as CoreType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', CoreType\TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Имя пользователя (латинскими буквами)'
            ])
            ->add('email', CoreType\EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'e-mail пользователя'
            ])
            ->add('password', CoreType\PasswordType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Пароль'
            ])
            ->add('enabled', CoreType\CheckboxType::class, [
                'label' => 'Активировать пользователя',
                'value' => true,
                'attr' => [
                    'class' => 'switcher pull-right'
                ],
                'required' => false,
            ])
            ->add('roles', CoreType\ChoiceType::class, [
                'label' => 'Статус',
                'choices' => User::USER_ROLES,
                'expanded' => false,
                'multiple' => false,
                'mapped' => false
            ])
            ->add('save', CoreType\SubmitType::class, [
               'attr' => [
                   'class' => 'btn btn-primary'
               ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}