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

class PortfolioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
		   ->add('seoTitle', TextType::class, [
				'label' => 'Мета-таг Title',
				'attr' => [
					'placeholder' => 'Не более 80 знаков'
				],
           ])
           ->add('seoKeywords', TextareaType::class, [
				'label' => 'Мета-таг Keywords',
				'attr' => [
					'placeholder' => 'Не более 250 знаков'
				],
           ])
           ->add('seoDescription', TextareaType::class, [
				'label' => 'Мета-таг Description',
				'attr' => [
					'placeholder' => 'Не более 200 знаков'
				],
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
