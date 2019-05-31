<?php
namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;

/**
 * Puzzle Learning Comment
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="puzzle_learning_comment")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment implements NodeInterface
{
    use Timestampable, Blameable, Node;
    
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var bool
     * @ORM\Column(name="is_visible", type="boolean")
     */
    private $isVisible;
    
    /**
     * @ORM\OneToMany(targetEntity="CommentVote", mappedBy="comment")
     */
    private $votes;

    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="comments")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function setContent($content) : self {
        $this->content = $content;
        return $this;
    }

    public function getContent() :? string {
        return $this->content;
    }

    public function setVisible($isVisible) : self {
        $this->isVisible = $isVisible;
        return $this;
    }

    public function isVisible() :? bool{
        return $this->isVisible;
    }

    public function setCourse(Course $course = null) : self {
        $this->course = $course;
        return $this;
    }

    public function getCourse() :? Course {
        return $this->course;
    }
    
    public function setVotes(Collection $votes) : self {
        $this->votes = $votes;
        return $this;
    }
    
    public function addVote(CommentVote $vote) : self {
        if ($this->votes === null || $this->votes->contains($vote) === false){
            $this->votes->add($vote);
        }
        
        return $this;
    }
    
    public function removeVote(CommentVote $vote) : self {
        $this->votes->removeElement($vote);
        return $this;
    }
    
    public function getVotes() :? Collection {
        return $this->votes;
    }
}
