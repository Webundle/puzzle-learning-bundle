<?php
namespace Puzzle\LearningBundle\Controller;

use Puzzle\LearningBundle\Entity\Category;
use Puzzle\LearningBundle\Entity\Comment;
use Puzzle\LearningBundle\Entity\Course;
use Puzzle\LearningBundle\Form\Type\CategoryCreateType;
use Puzzle\LearningBundle\Form\Type\CategoryUpdateType;
use Puzzle\LearningBundle\Form\Type\CourseCreateType;
use Puzzle\LearningBundle\Form\Type\CourseUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Puzzle\LearningBundle\LearningEvents;
use Puzzle\LearningBundle\Event\CourseEvent;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * Show categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request) {
        return $this->render("AdminBundle:Learning:list_categories.html.twig", array(
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findBy(['parentNode' => null])
        ));
    }
    
    /***
     * Show category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, Category $category) {
        $rep = $this->getDoctrine()->getRepository(Category::class);
        return $this->render("AdminBundle:Learning:show_category.html.twig", array(
            'category' => $category,
            'childNodes' => $rep->findBy(['parentNode' => $category->getId()])
        ));
    }
    
    /***
     * Create category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCategoryAction(Request $request) {
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if ($parentId !== null && $parent = $em->getRepository(Category::class)->find($parentId)){
            $category->setParentNode($parent);
        }
        
        $form = $this->createForm(CategoryCreateType::class, $category, [
            'method' => 'POST',
            'action' => $parentId ? 
                        $this->generateUrl('puzzle_admin_learning_category_create', ['parent' => $parentId]) : 
                        $this->generateUrl('puzzle_admin_learning_category_create')
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em->persist($category);
            $em->flush();
            
            $message = $this->get('translator')->trans('learning.category.create.success', [
                '%categoryName%' => $category->getName()
            ], 'learning');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            
            if (null !== $parentId) {
                return $this->redirectToRoute('puzzle_admin_learning_category_show', ['id' => $parentId]);
            }
            
            return $this->redirectToRoute('puzzle_admin_learning_category_list');
        }
        
        return $this->render("AdminBundle:Learning:create_category.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCategoryAction(Request $request, Category $category) {
        $parentId = $request->query->get('parent');
        
        $form = $this->createForm(CategoryUpdateType::class, $category, [
            'method' => 'POST', 
            'action' => $parentId ?
                        $this->generateUrl('puzzle_admin_learning_category_update', ['id' => $category->getId(), 'parent' => $parentId]) :
                        $this->generateUrl('puzzle_admin_learning_category_update', ['id' => $category->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('learning.category.update.success', [
                '%categoryName%' => $category->getName()
            ], 'learning');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            
            if (null !== $parentId) {
                return $this->redirectToRoute('puzzle_admin_learning_category_show', ['id' => $parentId]);
            }
            
            return $this->redirectToRoute('puzzle_admin_learning_category_show', ['id' => $category->getId()]);
        }
        
        return $this->render("AdminBundle:Learning:update_category.html.twig", array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCategoryAction(Request $request, Category $category) {
        $message = $this->get('translator')->trans('learning.category.delete.success', [
            '%categoryName%' => $category->getName()
        ], 'learning');
        $parent = $category->getParentNode();
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        
        if (null !== $parent) {
            return $this->redirectToRoute('puzzle_admin_learning_category_show', ['id' => $parent->getId()]);
        }
        
        return $this->redirectToRoute('puzzle_admin_learning_category_list');
    }
    
    
    /***
     * Show Courses
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCoursesAction(Request $request){
        return $this->render("AdminBundle:Learning:list_courses.html.twig", array(
            'courses' => $this->getDoctrine()->getRepository(Course::class)->findBy([])
        ));
    }
    
    /***
     * Show course
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCourseAction(Request $request, Course $course){
        return $this->render("AdminBundle:Learning:show_course.html.twig", array(
            'course' => $course
        ));
    }
    
    /***
     * Create course
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCourseAction(Request $request) {
        $course = new Course();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(CourseCreateType::class, $course, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_learning_course_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_learning_course_create'];
            
            $course->setTags($course->getTags() !== null ? explode(',', $course->getTags()) : null);
            $course->setEnableComments($course->getEnableComments() == "on" ? true : false);
            
            if (false === empty($data['startedAt'])) {
                $course->setStartedAt(new \DateTime($data['startedAt']));
            }
            
            if (false === empty($data['endedAt'])) {
                $course->setEndedAt(new \DateTime($data['endedAt']));
            }
            
            $em->persist($course);
            $em->flush();
            
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($picture !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setPicture($filename);}
                ]));
            }
            
            $audio = $request->request->get('audio') !== null ? $request->request->get('audio') : $data['audio'];
            if ($audio !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $audio,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setAudio($filename);}
                ]));
            }
            
            $video = $request->request->get('video') !== null ? $request->request->get('video') : $data['video'];
            if ($video !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $video,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setVideo($filename);}
                ]));
            }
            
            $document = $request->request->get('document') !== null ? $request->request->get('document') : $data['document'];
            if ($document !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $document,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setDocument($filename);}
                ]));
            }
            
            $dispatcher->dispatch(LearningEvents::LEARNING_COURSE_CREATED, new CourseEvent($course));
            
            $message = $this->get('translator')->trans('learning.course.create.success', [
                '%courseName%' => $course->getName()
            ], 'learning');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('puzzle_admin_learning_course_show', ['id' => $course->getId()]);
        }
        
        return $this->render("AdminBundle:Learning:create_course.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update course
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCourseAction(Request $request, Course $course) {
        $form = $this->createForm(CourseUpdateType::class, $course, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_learning_course_update', ['id' => $course->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_learning_course_update'];
            
            $course->setTags($course->getTags() !== null ? explode(',', $course->getTags()) : null);
            $course->setEnableComments($course->getEnableComments() == "on" ? true : false);
            
            if (false === empty($data['startedAt'])) {
                $course->setStartedAt(new \DateTime($data['startedAt']));
            }
            
            if (false === empty($data['endedAt'])) {
                $course->setEndedAt(new \DateTime($data['endedAt']));
            }
            
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($course->getPicture() === null || $course->getPicture() !== $picture) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setPicture($filename);}
                ]));
            }
            
            $audio = $request->request->get('audio') !== null ? $request->request->get('audio') : $data['audio'];
            if ($course->getAudio() === null || $course->getAudio() !== $audio) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $audio,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setAudio($filename);}
                ]));
            }
            
            $video = $request->request->get('video') !== null ? $request->request->get('video') : $data['video'];
            if ($course->getVideo() === null || $course->getVideo() !== $video) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $video,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setVideo($filename);}
                ]));
            }
            
            $document = $request->request->get('document') !== null ? $request->request->get('document') : $data['document'];
            if ($course->getDocument() === null || $course->getDocument() !== $document) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $document,
                    'context' => MediaUtil::extractContext(Course::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($course) {$course->setDocument($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('learning.course.update.success', [
                '%courseName%' => $course->getName()
            ], 'learning');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('puzzle_admin_learning_course_show', ['id' => $course->getId()]);
        }
        
        return $this->render("AdminBundle:Learning:update_course.html.twig", array(
            'course' => $course,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete Course
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCourseAction(Request $request, Course $course) {
        $message = $this->get('translator')->trans('learning.course.delete.success', [
            '%courseName%' => $course->getName()
        ], 'learning');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($course);
        $em->flush();
        
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_learning_course_list');
    }
    
    /***
     * Show Comments
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentsAction(Request $request, Course $course)
    {
        $comments = $this->getDoctrine()
                        ->getRepository(Comment::class)
                        ->findBy(['course' => $course->getId(), 'parentNode' => null],['createdAt' => 'DESC']);
        
        return $this->render("AdminBundle:Learning:list_comments.html.twig", array(
            'comments' => $comments
        ));
    }
    
    /***
     * Show comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCommentAction(Request $request, Comment $comment)
    {
        return $this->render("AdminBundle:Learning:show_comment.html.twig", array(
            'comment' => $comment
        ));
    }
    
    /**
     * Approve Comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function approveCommentAction(Request $request, Comment $comment)
    {   
        $comment->setVisible(true);
        $this->get('doctrine.orm.entity_manager')->flush();
        
        $message = $this->get('translator')->trans('learning.comment.approve.success', [
            '%author%' => $comment->getCreatedBy()
        ], 'learning');
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirect($this->generateUrl('puzzle_admin_learning_comment_list', ['id' => $comment->getCourse()->getId()]));
    }
    
    /**
     * Approve Comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function disapproveCommentAction(Request $request, Comment $comment)
    {
        $comment->setVisible(true);
        $this->get('doctrine.orm.entity_manager')->flush();
        
        $message = $this->get('translator')->trans('learning.comment.disapprove.success', [
            '%author%' => $comment->getCreatedBy()
        ], 'learning');
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirect($this->generateUrl('puzzle_admin_learning_comment_list', ['id' => $comment->getCourse()->getId()]));
    }
    
    /***
     * Delete comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, Comment $comment)
    {
        $course = $comment->getCourse();
        $message = $this->get('translator')->trans('learning.course.delete.success', [
            '%author%' => $comment->getCreatedBy()
        ], 'learning');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirect($this->generateUrl('puzzle_admin_learning_comment_list', ['id' => $course->getId()]));
    }
}
