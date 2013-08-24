<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Flashcard\Form\CategoryForm;
use Flashcard\Form\WeightForm;
use Flashcard\Entity\Category;
use Zend\Paginator\Paginator;

class CategoryController extends AbstractActionController
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
        $countQuery = 'SELECT COUNT(u.id) FROM Flashcard\Entity\Category u';
        $categoriesCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // RESULT QUERY
        $resultQuery = 'SELECT u FROM Flashcard\Entity\Category u';
        $categories = $this->getEntityManager()->createQuery($resultQuery)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        $paginator = new Paginator(new \Zend\Paginator\Adapter\Null($categoriesCount));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        return new ViewModel(array(
            'categories' => $categories,
            'paginator' => $paginator,
        ));
    }

    public function addAction()
    {
        $domain_id = (int) $this->params('id');

        $domains = $this
            ->getEntityManager()
            ->getRepository('Flashcard\Entity\Domain')
            ->findAll();
        $domains_array = array();
        foreach ($domains as $domain)
        {
            $domains_array[$domain->getId()] = $domain->getName();
        }

        $form = new CategoryForm('category', $domains_array, $domain_id);
        $form->setValidationGroup('csrf', 'name', 'domain_option');

        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $category = new Category();
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $category->setName($data['name']);
                $domain = $this->getEntityManager()
                    ->find('Flashcard\Entity\Domain', $data['domain_option']);
                $category->setdomain($domain);
                $this->getEntityManager()->persist($category);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Category was successfully added.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem adding the category.');
                }

                // Redirect to list of categories.
                return $this->redirect()
                    ->toRoute('domain', array('action' => 'view', 'id' => $domain->getId()));
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params('id');
        $category = $this->getEntityManager()->find('Flashcard\Entity\Category', $id);

        if (!$id || !$category)
        {
            return $this->redirect()->toRoute('category');
        }

        $domains = $this
            ->getEntityManager()
            ->getRepository('Flashcard\Entity\Domain')
            ->findAll();
        $domains_array = array();
        foreach ($domains as $domain)
        {
            $domains_array[$domain->getId()] = $domain->getName();
        }

        $form = new CategoryForm('category', $domains_array, $category->getDomain()->getId());

        $form->setBindOnValidate(false);
        $form->bind($category);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid())
            {
                $category->setName($data['name']);
                $domain = $this->getEntityManager()
                    ->find('Flashcard\Entity\Domain', $data['domain_option']);
                $category->setdomain($domain);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Category was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem saving the category.');
                }
                // Redirect to list of categories.
                return $this->redirect()
                    ->toRoute('domain', array('action' => 'view', 'id' => $domain->getId()));
            }
            else {
                $form->setData($request->getPost());
            }
        }

        return new ViewModel(array(
            'id' => $id,
            'form' => $form,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('category');
        }

        $category = $this->getEntityManager()->find('Flashcard\Entity\Category', $id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->getEntityManager()->remove($category);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Category was successfully deleted.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a deleting the category.');
                }
            }

            // Redirect to list of variables.
            return $this->redirect()->toRoute('category');
        }

        return new ViewModel(array(
            'id' => $id,
            'category' => $category,
        ));
    }

    public function viewAction()
    {
        $category_id = (int) $this->params('id');
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

        // GET CATEGORY
        $category = $this->getEntityManager()->find('Flashcard\Entity\Category', $category_id);

        // COUNT QUERY
        $countQuery = "SELECT COUNT(u.id) FROM Flashcard\Entity\Question u WHERE u.category={$category_id}";
        $questionsCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // RESULT QUERY
        $resultQuery = "SELECT u FROM Flashcard\Entity\Question u WHERE u.category={$category_id}";
        $questions = $this->getEntityManager()->createQuery($resultQuery)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();
        
        // Set the hidden weight forms.
        foreach ($questions as $question) {
            $question->weightForm = new WeightForm();
            $question->weightForm->setAttribute('id', 'weight-' . $question->getId());
            $question->weightForm->get('csrf')->setAttribute('id', 'csrf-' . $question->getId());
        }

        $paginator = new Paginator(new \Zend\Paginator\Adapter\Null($questionsCount));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        
        $this->addJqueryUi();

        return new ViewModel(array(
            'questions' => $questions,
            'paginator' => $paginator,
            'category_id' => $category_id,
            'category' => $category,
            'offset' => $offset,
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
    
    public function addJqueryUi()
    {
        $viewHelperJs = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $viewHelperJs->appendFile('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');

        $viewHelperCss = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadLink');
        $viewHelperCss->appendStylesheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/overcast/jquery-ui.css');
    }

}
