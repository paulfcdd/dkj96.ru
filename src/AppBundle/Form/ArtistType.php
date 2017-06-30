<?php

namespace AppBundle\Form;


use AppBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class ArtistType extends AbstractFormType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
       parent::buildForm($builder, $options);

       $builder
           ->add('files', FileUploadType::class);
   }
}