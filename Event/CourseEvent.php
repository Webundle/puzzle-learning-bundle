<?php 

namespace Puzzle\LearningBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Puzzle\LearningBundle\Entity\Course;

class CourseEvent extends Event
{
	/**
	 * @var Course
	 */
	private $course;
	
	/**
	 * @var array
	 */
	private $data;
	
	public function __construct(Course $course, array $data = null){
		$this->course= $course;
		$this->data = $data;
	}
	
	public function getCourse(){
		return $this->course;
	}
	
	public function getData(){
	    return $this->data;
	}
	
}

?>