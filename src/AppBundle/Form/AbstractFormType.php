<?php

namespace AppBundle\Form;


use AppBundle\Form\Type\CKeditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('title', TextType::class, [
               'label' => 'Название'
           ])
           ->add('description', TextareaType::class, [
               'label' => 'Краткое описание'
           ])
           ->add('content', CKeditorType::class, [
               'label' => 'Полная информация'
           ]);
    }
}