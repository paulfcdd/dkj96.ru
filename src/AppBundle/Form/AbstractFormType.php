<?php

namespace AppBundle\Form;


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
               'attr' => [
                   'class' => 'form-control'
               ],
               'label' => 'Название'
           ])
           ->add('description', TextareaType::class, [
               'attr' => [
                   'class' => 'form-control no-resize',
                   'cols' => 3,
                   'rows' => 3
               ],
               'label' => 'Краткое описание'
           ])
           ->add('content', TextareaType::class, [
               'attr' => [
                   'class' => 'form-control ckeditor',
               ],
               'label' => 'Полная информация'
           ]);
    }
}