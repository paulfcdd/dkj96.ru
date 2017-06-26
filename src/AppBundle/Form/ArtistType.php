<?php

namespace AppBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class ArtistType extends AbstractFormType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
       parent::buildForm($builder, $options);

       $builder
           ->add('files', FileType::class, [
               'label' => 'Файлы для загрузки',
               'required' => false,
               'mapped' => false,
               'multiple' => true,
           ]);   }
}