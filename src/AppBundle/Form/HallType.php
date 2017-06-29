<?php

namespace AppBundle\Form;


use AppBundle\Entity\Hall;
use AppBundle\Form\Type\CKeditorType;
use AppBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ])
            ->add('specification', CKeditorType::class, [
                'label' => 'Спецификация'
            ])
            ->add('files', FileUploadType::class);
    }
}