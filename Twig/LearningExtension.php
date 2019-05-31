<?php
namespace Puzzle\LearningBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\LearningBundle\Entity\Course;
use Puzzle\LearningBundle\Entity\Category;
use Puzzle\LearningBundle\Entity\Archive;
use Puzzle\LearningBundle\Entity\Comment;
use Puzzle\LearningBundle\Entity\CommentVote;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class LearningExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    protected $em;
    
    /**
     * @var Paginator $paginator
     */
    protected $paginator;
    
    public function __construct(EntityManager $em, Paginator $paginator) {
        $this->em = $em;
        $this->paginator = $paginator;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('puzzle_learning_categories', [$this, 'getCategories'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_category', [$this, 'getCategory'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_courses', [$this, 'getCourses'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_course', [$this, 'getCourse'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_archives', [$this, 'getArchives'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_archive', [$this, 'getArchive'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_comments', [$this, 'getComments'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_comment', [$this, 'getComment'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_comment_votes', [$this, 'getCommentVotes'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_learning_comment_vote', [$this, 'getCommentVote'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getCategories(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Category::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Category::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getCategory($id) {
        if (!$category = $this->em->find(Category::class, $id)) {
            $category =  $this->em->getRepository(Category::class)->findOneBy(['slug' => $id]);
        }
        
        return $category;
    }
    
    public function getArchives(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Archive::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Archive::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getArchive($id) {
        return $this->em->find(Archive::class, $id);
    }
    
    public function getCourses(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Course::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Course::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getCourse($id) {
        if (!$course = $this->em->find(Course::class, $id)) {
            $course =  $this->em->getRepository(Course::class)->findOneBy(['slug' => $id]);
        }
        
        return $course;
    }
    
    public function getComments(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Comment::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Comment::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getComment($id) {
        return $this->em->find(Comment::class, $id);
    }
    
    public function getCommentVotes(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = 5, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(CommentVote::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(CommentVote::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getCommentVote($id) {
        return $this->em->find(CommentVote::class, $id);
    }
}
