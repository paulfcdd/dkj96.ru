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
               'label' => 'Заголовок'
           ])
           ->add('content', TextareaType::class, [
               'attr' => [
                   'class' => 'form-control',
               ],
           ])
           ->add('metaKeywords', TextType::class, [
               'attr' => [
                   'class' => 'form-control'
               ],
           ])
           ->add('metaTitle', TextType::class, [
               'attr' => [
                   'class' => 'form-control'
               ],
           ])
           ->add('metaDescription', TextareaType::class, [
               'attr' => [
                   'class' => 'form-control'
               ],
           ])
           ->add('save', SubmitType::class, [
               'attr' => [
                   'class' => 'btn btn-primary',
                   'role' => 'button'
               ],
               'label' => 'Добавить'
           ]);
    }
}