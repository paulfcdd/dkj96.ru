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

class PortfolioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
