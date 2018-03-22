<?php

namespace AppBundle\Form;


use AppBundle\Entity\EventDateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class EventDateTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', TextType::class, [
                'attr' => [
                    'data-type' => 'event-date'
                ]
            ])
            ->add('time', TextType::class, [
                'attr' => [
                    'data-type' => 'event-time'
                ]
            ])
            ->add('kassyRuPID', TextType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventDateTime::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'event_date_time';
    }
}