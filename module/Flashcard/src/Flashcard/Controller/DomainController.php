<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Flashcard\Form\DomainForm;
use Flashcard\Form\WeightForm;
use Flashcard\Entity\Domain;
use Zend\Paginator\Paginator;

class DomainController extends AbstractActionController
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
        $countQuery = 'SELECT COUNT(u.id) FROM Flashcard\Entity\Domain u';
        $domainsCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // RESULT QUERY
        $resultQuery = 'SELECT u FROM Flashcard\Entity\Domain u ORDER BY u.weight ASC';
        $domains = $this->getEntityManager()->createQuery($resultQuery)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        // Set the hidden weight forms.
        foreach ($domains as $domain) {
            $domain->weightForm = new WeightForm();
            $domain->weightForm->setAttribute('id', 'weight-' . $domain->getId());
            $domain->weightForm->get('csrf')->setAttribute('id', 'csrf-' . $domain->getId());
        }

        $paginator = new Paginator(new \Zend\Paginator\Adapter\Null($domainsCount));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        $this->addJqueryUiSortableWeight();

        return new ViewModel(array(
            'domains' => $domains,
            'paginator' => $paginator,
            'offset' => $offset,
        ));
    }

    public function addAction()
    {
        // COUNT QUERY
        $countQuery = 'SELECT COUNT(u.id) FROM Flashcard\Entity\Domain u';
        $domainCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // Setup domain count.
        $domainCountArray = array();
        for ($i = 0; $i < $domainCount + 1; $i++) {
            $domainCountArray[$i + 1] = $i + 1;
        }

        $form = new DomainForm('domain', $domainCountArray, $domainCount + 1);
        $form->setValidationGroup('csrf', 'name', 'weight');

        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $domain = new Domain();
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $domain->setName($data['name']);
                $domain->setWeight($data['weight']);
                $this->getEntityManager()->persist($domain);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Domain was successfully added.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem adding the domain.');
                }

                // Redirect to list of albums
                return $this->redirect()->toRoute('domain');
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params('id');
        $domain = $this->getEntityManager()->find('Flashcard\Entity\Domain', $id);

        if (!$id || !$domain)
        {
            return $this->redirect()->toRoute('domain');
        }

        // COUNT QUERY
        $countQuery = 'SELECT COUNT(u.id) FROM Flashcard\Entity\Domain u';
        $domainCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // Setup domain count.
        $domainCountArray = array();
        for ($i = 0; $i < $domainCount; $i++) {
            $domainCountArray[$i + 1] = $i + 1;
        }

        $form = new DomainForm('domain', $domainCountArray, $domain->getWeight());

        $form->setBindOnValidate(false);
        $form->bind($domain);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid())
            {
                $domain->setName($data['name']);
                $domain->setWeight($data['weight']);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Domain was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem saving the domain.');
                }
                // Redirect to list of domains.
                return $this->redirect()->toRoute('domain');
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
            return $this->redirect()->toRoute('domain');
        }

        $domain = $this->getEntityManager()->find('Flashcard\Entity\Domain', $id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->getEntityManager()->remove($domain);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Domain was successfully deleted.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a deleting the domain.');
                }
            }

            // Redirect to list of variables.
            return $this->redirect()->toRoute('domain');
        }

        return new ViewModel(array(
            'id' => $id,
            'domain' => $domain,
        ));
    }

    public function viewAction()
    {
        $domain_id = (int) $this->params('id');
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

        // GET DOMAIN
        $domain = $this->getEntityManager()->find('Flashcard\Entity\Domain', $domain_id);

        // COUNT QUERY
        $countQuery = "SELECT COUNT(u.id) FROM Flashcard\Entity\Category u WHERE u.domain={$domain_id}";
        $categoriesCount = $this->getEntityManager()->createQuery($countQuery)
            ->getSingleScalarResult();

        // RESULT QUERY
        $resultQuery = "SELECT u FROM Flashcard\Entity\Category u WHERE u.domain={$domain_id} ORDER BY u.weight ASC";
        $categories = $this->getEntityManager()->createQuery($resultQuery)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        // Set the hidden weight forms.
        foreach ($categories as $category) {
            $category->weightForm = new WeightForm();
            $category->weightForm->setAttribute('id', 'weight-' . $category->getId());
            $category->weightForm->get('csrf')->setAttribute('id', 'csrf-' . $category->getId());
        }

        $paginator = new Paginator(new \Zend\Paginator\Adapter\Null($categoriesCount));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        $this->addJqueryUiSortableWeight();

        return new ViewModel(array(
            'categories' => $categories,
            'paginator' => $paginator,
            'domain_id' => $domain_id,
            'domain' => $domain,
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

    public function addJqueryUiSortableWeight()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $viewHelperJs = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $viewHelperCss = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadLink');

        $viewHelperJs->appendFile('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');
        $viewHelperJs->appendFile($renderer->basePath() . '/javascripts/sortableItems.js');


        $viewHelperCss->appendStylesheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/overcast/jquery-ui.css');
    }

}
