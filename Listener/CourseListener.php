<?php

namespace Puzzle\LearningBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\LearningBundle\Entity\Archive;
use Puzzle\LearningBundle\Event\CourseEvent;

class CourseListener
{
    /**
     * @var EntityManager
     */
    private $em;
    
    public function __construct(EntityManager $em){
        $this->em = $em;
    }
    
    public function onCreated(CourseEvent $event)
    {
        $course = $event->getCourse();
        $now = new \DateTime();
        $archive = $this->em->getRepository(Archive::class)->findOneBy([
            'month' => (int) $now->format("m"),
            'year' => $now->format("Y")
        ]);
        
        if ($archive === null) {
            $archive = new Archive();
            $archive->setMonth((int) $now->format("m"));
            $archive->setYear($now->format("Y"));
            
            $this->em->persist($archive);
        }
        
        $course->setArchive($archive);
        
        $this->em->flush($course);
    }
}

?>
