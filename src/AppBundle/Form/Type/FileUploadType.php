<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileUploadType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Файлы для загрузки',
            'required' => false,
            'mapped' => false,
            'multiple' => true,
            'attr' => [
                'enctype' => "multipart/form-data"
            ]
        ]);
    }

    public function getParent()
    {
        return FileType::class;
    }
}