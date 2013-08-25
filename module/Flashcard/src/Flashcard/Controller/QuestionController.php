<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Flashcard\Form\QuestionForm;
use Flashcard\Entity\Question;
use Zend\Paginator\Paginator;

class QuestionController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $page = (int) $this->params('page');
        $limit = 10;
        switch ($page)
        {
            case null:
                $offset = 0;
                break;
            case 1:
                $offset = 0;
                break;
            default :
                $offset = ($page - 1) * $limit;
                break;
        }

         // COUNT QUERY
        $countQuery = 'SELECT COUNT(u.id) FROM Flashcard\Entity\Question u';
        $questionsCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // RESULT QUERY
        $resultQuery = 'SELECT u FROM Flashcard\Entity\Question u';
        $questions = $this->getEntityManager()->createQuery($resultQuery)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        $paginator = new Paginator(new \Zend\Paginator\Adapter\Null($questionsCount));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        //$questions = $this->getEntityManager()->getRepository('Flashcard\Entity\Question')->findAll();

        //$this->layout('layout/admin-layout');
        return new ViewModel(array(
            'questions' => $questions,
            'paginator' => $paginator,
        ));
    }

    public function addAction()
    {
        $category_id = (int) $this->params('id');
        $categories = $this->getEntityManager()->getRepository('Flashcard\Entity\Category')->findAll();
        $categories_array = array();
        foreach ($categories as $category) {
            $categories_array[$category->getId()] = $category->getName();
        }

        $set_value = ($category_id) ? array($category_id) : array();
        $form = new QuestionForm('question', $categories_array, $set_value);
        $form->setValidationGroup('csrf', 'question', 'answer', 'category_options');

        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $question = new Question();
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $question->setQuestion($data['question']);
                $question->setAnswer($data['answer']);
                $question->setNote($data['note']);
                $category = $this->getEntityManager()
                        ->find('Flashcard\Entity\Category', $data['category_options']);
                $question->setCategory($category);
                $this->getEntityManager()->persist($question);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Question was successfully added.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem adding the question.');
                }

                // Redirect to list of albums
                return $this->redirect()
                        ->toRoute('category', array('action' => 'view', 'id' => $category->getId()));
            }
        }

        $this->addMarkItUp();

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params('id');
        $question = $this->getEntityManager()->find('Flashcard\Entity\Question', $id);

        if (!$id || !$question)
        {
            return $this->redirect()->toRoute('question');
        }

        $categories = $this->getEntityManager()->getRepository('Flashcard\Entity\Category')->findAll();
        $categories_array = array();
        $question_category = $question->getCategory()->getId();
        $set_value = array();
        foreach ($categories as $category) {
            $categories_array[$category->getId()] = $category->getName();
            if ($category->getId() == $question_category) {
                $set_value[] = $category->getId();
            }
        }


        $form = new QuestionForm('question', $categories_array, $set_value);

        $form->setBindOnValidate(false);
        $form->bind($question);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid())
            {
                $question->setQuestion($data['question']);
                $question->setAnswer($data['answer']);
                $question->setNote($data['note']);
                $new_category = $this->getEntityManager()
                        ->find('Flashcard\Entity\Category', $data['category_options']);
                $question->setCategory($new_category);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Question was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem saving the question.');
                }
                // Redirect to list of albums
                return $this
                    ->redirect()
                    ->toRoute('category', array(
                                            'action' => 'view',
                                            'id' => $question->getCategory()->getId(),
                                        ));
            }
            else {
                $form->setData($request->getPost());
            }
        }

        $this->addMarkItUp();

        return new ViewModel(array(
            'id' => $id,
            'form' => $form,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('question');
        }

        $category = $this->getEntityManager()->find('Flashcard\Entity\Question', $id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->getEntityManager()->remove($category);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Question was successfully deleted.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a deleting the question');
                }
            }

            // Redirect to list of variables.
            return $this->redirect()->toRoute('question');
        }

        return new ViewModel(array(
            'id' => $id,
            'question' => $category,
        ));
    }

    public function viewAction()
    {
        $id = (int) $this->params('id');
        $question = $this->getEntityManager()->find('Flashcard\Entity\Question', $id);
        if (!$id || !$question)
        {
            return $this->redirect()->toRoute('question');
        }

        return new ViewModel(array(
            'question' => $question,
            'id' => $id,
        ));
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function addMarkItUp()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $viewHelperJs = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $viewHelperCss = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadLink');

        $viewHelperJs->appendFile($renderer->basePath() . '/javascripts/markitup/jquery.markitup.js');
        $viewHelperJs->appendFile($renderer->basePath() . '/javascripts/markitup/sets/html/set.js');
        $viewHelperJs->appendFile($renderer->basePath() . '/javascripts/setMarkItUp.js');

        $viewHelperCss->appendStylesheet($renderer->basePath() . '/javascripts/markitup/skins/markitup/style.css');
        $viewHelperCss->appendStylesheet($renderer->basePath() . '/javascripts/markitup/sets/html/style.css');
    }

}
