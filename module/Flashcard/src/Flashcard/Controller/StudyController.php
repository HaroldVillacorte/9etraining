<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class StudyController extends AbstractActionController
{
    protected $em;

    public function indexAction()
    {
        $query = 'SELECT u FROM Flashcard\Entity\Category u JOIN Flashcard\Entity\Domain'
                . ' d WHERE d = u.domain ORDER BY d.weight, u.weight ASC';
        $categories = $this->getEntityManager()->createQuery($query)
            ->getResult();

        return new ViewModel(array(
            'categories' => $categories,
        ));
    }

    public function categoryAction()
    {
        $id = (int) $this->params('id');

        $category = $this->getEntityManager()->find('Flashcard\Entity\Category', $id);
        $categoryId = $category->getId();

        if (!$id || !$category) {
            $this->redirect()->toRoute('study');
        }

        $query = "SELECT u FROM Flashcard\Entity\Question u"
                . " WHERE u.category = {$categoryId} ORDER BY u.weight ASC";

        $questions = $this->getEntityManager()->createQuery($query)
            ->getResult();

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
