<?php

namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Archive
 *
 * @ORM\Table(name="puzzle_learning_archive")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\ArchiveRepository")
 */
class Archive
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="month", type="string", length=255)
     */
    private $month;

    /**
     * @var string
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;
    
    /**
     * @ORM\OneToMany(targetEntity="Course", mappedBy="archive")
     */
    private $courses;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getId() :?int {
        return $this->id;
    }

    public function setMonth($month) :self {
        $this->month = $month;
        return $this;
    }

    public function getMonth() :?int {
        return $this->month;
    }

    public function setYear($year) :self {
        $this->year = $year;
        return $this;
    }
    
    public function getYear() :?int {
        return $this->year;
    }
    
    public function addCourse(Course $course) :self {
        $this->courses[] = $course;
        return $this;
    }
    
    public function removeCourse(Course $course) :self {
        $this->courses->removeElement($course);
        return $this;
    }
    
    public function getCourses(){
        return $this->courses;
    }
    
    public function __toString(){
        return $this->month.'/'. $this->year;
    }
}
