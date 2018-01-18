<?php
/**
 * Created by PhpStorm.
 * User: paulf
 * Date: 2017-12-20
 * Time: 01:33
 */

namespace AppBundle\Form;


use AppBundle\Entity\TopNavbar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as CoreType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopNavbarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isLink', CoreType\CheckboxType::class, [
                'label' => 'Ссылка',
                'value' => '0',
                'required' => false,
            ])
            ->add('url', CoreType\UrlType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'disabled' => true
                ]
            ])
            ->add('sortOrder', CoreType\IntegerType::class, [
                'label' => 'Порядок сортировки',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('icon', CoreType\ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'choices' => TopNavbar::ICONS,
                'label' => 'Иконка',
                'placeholder' => 'Нет',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('content', CoreType\TextType::class, [
                'label' => 'Текст',
                'attr' => [
                    'class' => 'form-control'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TopNavbar::class
        ]);
    }
}