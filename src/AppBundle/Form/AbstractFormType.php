<?php

namespace AppBundle\Form;


use AppBundle\Form\Type\CKeditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AbstractFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('seoTitle', TextType::class, [
				'label' => 'Мета-таг Title',
				'required' => false,
				'attr' => [
					'placeholder' => 'Не более 80 знаков'
				],
           ])
           ->add('seoKeywords', TextareaType::class, [
				'label' => 'Мета-таг Keywords',
				'required' => false,
				'attr' => [
					'placeholder' => 'Не более 250 знаков'
				],
           ])
           ->add('seoDescription', TextareaType::class, [
				'label' => 'Мета-таг Description',
				'required' => false,
				'attr' => [
					'placeholder' => 'Не более 200 знаков'
				],
           ])
           ->add('slug', TextType::class, [
				'label' => 'Задайте URL записи',
				'required' => false,
           ])
           ->add('redirect', ChoiceType::class, [
				'multiple' => false,
				'expanded' => true,
				'label' => 'Установить редирект?',
				'choices' => [
					'Да' => 1,
					'Нет' => 0,
				]
           ])
           ->add('redirectUrl', TextType::class, [
				'label' => 'Введите URL для редиректа',
				'attr' => [
					'placeholder' => "Подайте урл вида '/', '/event/23' и т.д."
				],
				'required' => false,	           
			])
           ->add('title', TextType::class, [
               'label' => 'Название',
               'required' => false,
           ])
           ->add('description', TextareaType::class, [
               'label' => 'Краткое описание'
           ])
           ->add('content', CKeditorType::class, [
               'label' => 'Полная информация'
           ]);
    }
}
