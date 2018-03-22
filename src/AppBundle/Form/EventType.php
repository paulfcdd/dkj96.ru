<?php

namespace AppBundle\Form;


use AppBundle\Entity\Event;
use AppBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('price', TextType::class, [
                'label' => 'Цена билета (в руб.)',
                'required' => false,
            ])
            ->add('ticketUrl', UrlType::class, [
                'label' => 'Ссылка на покупку билета (не обязательно)',
                'required' => false,
            ])
            ->add('files', FileUploadType::class)
            ->add('widgetJsCode', TextareaType::class, [
                'required' => false,
            ])
            ->add('widgetCssCode', TextareaType::class, [
                'required' => false,
            ])
            ->add('widgetHtmlCode', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'cols' => 10,
                    'rows' => 10,
                ]
            ])
            ->add('eventDateTime', CollectionType::class, [
                'entry_type' => EventDateTimeType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class
        ]);
    }
}