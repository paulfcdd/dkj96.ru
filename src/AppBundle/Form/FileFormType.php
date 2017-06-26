<?php
/**
 * Created by PhpStorm.
 * User: paulnovikov
 * Date: 26.06.17
 * Time: 20:30
 */

namespace AppBundle\Form;


use AppBundle\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('attachment', FileType::class, [
            'label' => 'Файлы для загрузки',
        ]);
    }
}