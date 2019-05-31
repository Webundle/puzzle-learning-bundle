<?php

namespace Puzzle\LearningBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\LearningBundle\Form\Model\AbstractCourseType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class CourseUpdateType extends AbstractCourseType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'course_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'puzzle_admin_learning_course_update';
    }
}