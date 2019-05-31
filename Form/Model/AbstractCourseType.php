<?php

namespace Puzzle\LearningBundle\Form\Model;

use Puzzle\LearningBundle\Entity\Category;
use Puzzle\LearningBundle\Entity\Course;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\UserBundle\Entity\User;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('speaker', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName'
            ])
            ->add('tags', TextType::class, ['required' => false])
            ->add('picture', HiddenType::class, ['mapped' => false, 'required' => false])
            ->add('startedAt', TextType::class, ['mapped' => false, 'required' => false])
            ->add('endedAt', TextType::class, ['mapped' => false, 'required' => false])
            ->add('location', TextType::class, ['required' => false])
            ->add('audio', HiddenType::class, ['mapped' => false, 'required' => false])
            ->add('video', HiddenType::class, ['mapped' => false, 'required' => false])
            ->add('document', HiddenType::class, ['mapped' => false, 'required' => false])
            ->add('enableComments', CheckboxType::class, ['required' => false])
            ->add('visible', CheckboxType::class, ['required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Course::class,
            'validation_groups' => array(
                Course::class,
                'determineValidationGroups',
            ),
        ));
    }
}