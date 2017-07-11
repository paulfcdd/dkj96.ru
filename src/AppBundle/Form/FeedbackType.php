<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius',
                    'placeholder' => 'Ваш e-mail'
                ],
                'label' => false,
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius',
                    'placeholder' => 'Ваше имя и фамилия'
                ],
                'label' => false,

            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control no-resize no-border-radius',
                    'placeholder' => 'Ваше сообщение',
                    'cols' => 3,
                    'rows' => 3
                ],
                'label' => false,
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Feedback'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_feedback';
    }


}
