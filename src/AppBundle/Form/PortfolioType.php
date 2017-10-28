<?php

namespace AppBundle\Form;


use AppBundle\Entity\Event;
use AppBundle\Entity\Portfolio;
use AppBundle\Form\Type\CKeditorType;
use AppBundle\Form\Type\FileUploadType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PortfolioType extends AbstractType
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
				'required' => false,
				'label' => 'Введите URL для редиректа',
				'attr' => [
					'placeholder' => "Подайте урл вида '/', '/event/23' и т.д."
				],
				'required' => false,	           
			])
            ->add('title', TextType::class)
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title'
            ])
            ->add('description', CKeditorType::class)
            ->add('files', FileUploadType::class, [
                'label' => 'Допустимые форматы - JPG/PNG/MP4/MPEG. Для обложки альбома необходимо загружать квадратные изображения'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
        ]);
    }
}
