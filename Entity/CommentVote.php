<?php
namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Puzzle Learning CommentVote
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="puzzle_learning_comment_vote")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\CommentVoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CommentVote
{
    use Timestampable, Blameable;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="votes")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     */
    private $comment;
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function setComment(Comment $comment = null) : self {
        $this->comment = $comment;
        return $this;
    }

    public function getComment() :? Comment {
        return $this->comment;
    }
}
