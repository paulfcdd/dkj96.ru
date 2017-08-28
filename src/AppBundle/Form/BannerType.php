<?php

namespace AppBundle\Form;


use AppBundle\Entity\Banner;
use AppBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alt', TextType::class)
            ->add('files', FileUploadType::class, [
                'multiple' => false,
            ])
            ->add('link', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
        ]);
    }
}