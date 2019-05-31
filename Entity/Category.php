<?php
namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;

/**
 * Puzzle Learning Category
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="puzzle_learning_category")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category implements NodeInterface
{
    use Timestampable,  Blameable, Sluggable, Node;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;
    
    /**
     * @var string
     * @ORM\Column(name="materializedPath", type="string", nullable=true)
     */
    protected $materializedPath = '';
    
    /**
     * @ORM\OneToMany(targetEntity="Course", mappedBy="category")
     */
    private $courses;
    
    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function getNodeId() {
        return is_null($this->id) ? -1 : $this->id;
    }
    
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :?string {
        return $this->name;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setPicture($picture) :self {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() :?string {
        return $this->picture;
    }
    
    public function setCourses(Collection $courses) : self {
        $this->courses = $courses;
        return $this;
    }
    
    public function addCourse(Course $course) : self {
        if ($this->courses === null || $this->courses->contains($course) === false){
            $this->courses->add($course);
        }
        
        return $this;
    }
    
    public function removeCourse(Course $course) : self {
        $this->courses->removeElement($course);
        return $this;
    }
    
    public function getCourses() :? Collection {
        return $this->courses;
    }
}
