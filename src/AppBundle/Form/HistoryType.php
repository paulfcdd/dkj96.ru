<?php

namespace AppBundle\Form;


use AppBundle\Entity\History;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoryType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('enabled', ChoiceType::class, [
            'attr' => [
                'class' => 'form-control no-border-radius'
            ],
            'label' => 'Показывать эту статью на сайте',
            'expanded' => false,
            'multiple' => false,
            'choices' => [
                'Да' => 1,
                'Нет' => 0
            ],
            'mapped' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => History::class]);
    }
}