<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Flashcard\Form\CategoryForm;
use Flashcard\Entity\Category;

class CategoryController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $categories = $this->getEntityManager()->getRepository('Flashcard\Entity\Category')->findAll();

        //$this->layout('layout/admin-layout');
        return new ViewModel(array(
            'categories' => $categories,
        ));
    }

    public function addAction()
    {
        $form = new CategoryForm('category');
        $form->setValidationGroup('csrf', 'name');

        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $category = new Category();
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {                
                $category->setName($data['name']);
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

                // Redirect to list of albums
                return $this->redirect()->toRoute('category');
            }
        }

        //$this->layout('layout/admin-layout');
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

        $form = new CategoryForm();

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
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Category was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem saving the category.');
                }
                // Redirect to list of albums
                return $this->redirect()->toRoute('category');
            }
            else {
                $form->setData($request->getPost());
            }
        }

        //$this->layout('layout/admin-layout');
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

        //$this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'category' => $category,
        ));
    }

    /*public function viewAction()
    {
        $id = (int) $this->params('id');
        $category = $this->getEntityManager()->find('Music\Entity\Genre', $id);
        if (!$id || !$category)
        {
            return $this->redirect()->toRoute('category');
        }
        $artists = $category->getArtists();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'category' => $category,
            'artists' => $artists,
        ));
    }*/

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

}
