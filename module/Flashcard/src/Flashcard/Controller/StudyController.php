<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class StudyController extends AbstractActionController
{
    protected $em;
    
    public function indexAction()
    { 
        $catgories = $this->getEntityManager()
                ->getRepository('Flashcard\Entity\Category')
                ->findAll();
        return new ViewModel(array(
            'categories' => $catgories,
        ));        
    }
    
    public function categoryAction()
    {
        $id = (int) $this->params('id');
        $category = $this->getEntityManager()->find('Flashcard\Entity\Category', $id);
        if (!$id || !$category) {
            $this->redirect()->toRoute('study');
        }
        $questions = $category->getQuestions();
        
        return new ViewModel(array(
            'category' => $category,
            'questions' => $questions,
        ));
    }
    
    public function getEntityManager()
    {
        if (!$this->em)
        {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
}
