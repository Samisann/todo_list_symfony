<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('Illustration', FileType::class, [
            'label' => 'Illustration (JPG ou PNG)',
            'mapped' => false,
            'required' => false
        ])
            ->add('Title')
            ->add('Description')
            ->add('Status', ChoiceType::class, [
                'choices'  => [
                    'Todo' => 'Todo',
                    'Doing' => 'Doing',
                    'Done' => 'Done',
                ],
            ])
            ->add('Date')
       

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
