<?php

namespace AppBundle\Form;


use AppBundle\Form\Type as AppFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;



class AbstractFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('seoTitle', FormType\TextType::class, [
				'label' => 'Мета-таг Title',
				'required' => false,
				'attr' => [
					'placeholder' => 'Не более 80 знаков',

				],
           ])
           ->add('seoKeywords', FormType\TextareaType::class, [
				'label' => 'Мета-таг Keywords',
				'required' => false,
				'attr' => [
					'placeholder' => 'Не более 250 знаков'
				],
           ])
           ->add('seoDescription', FormType\TextareaType::class, [
				'label' => 'Мета-таг Description',
				'required' => false,
				'attr' => [
					'placeholder' => 'Не более 200 знаков'
				],
           ])
           ->add('slug', FormType\TextType::class, [
				'label' => 'Задайте URL записи',
				'required' => false,
           ])
           ->add('redirect', FormType\ChoiceType::class, [
				'multiple' => false,
				'expanded' => true,
				'label' => 'Установить редирект?',
				'choices' => [
					'Да' => 1,
					'Нет' => 0,
				]
           ])
           ->add('redirectUrl', FormType\TextType::class, [
				'label' => 'Введите URL для редиректа',
				'attr' => [
					'placeholder' => "Подайте урл вида '/', '/event/23' и т.д."
				],
				'required' => false,	           
			])
           ->add('title', FormType\TextType::class, [
               'label' => 'Название',
               'required' => false,
           ])
           ->add('description', FormType\TextareaType::class, [
               'label' => 'Краткое описание'
           ])
           ->add('content', AppFormType\CKeditorType::class, [
               'label' => 'Полная информация'
           ])
           ->add('save', FormType\SubmitType::class, [
               'attr' => [
                   'class' => 'btn btn-success'
               ],
               'label' => 'Сохранить'
           ]);
    }
}
