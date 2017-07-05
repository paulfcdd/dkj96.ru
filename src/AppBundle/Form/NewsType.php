<?php

namespace AppBundle\Form;


use AppBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('files', FileUploadType::class)
            ->add('publishStartDate', DateType::class, [
                'label' => 'Старт публикации',
            ])
            ->add('publishEndDate', DateType::class, [
                'label' => 'Конец публикации'
            ]);

    }
}