<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Flashcard\Form\WeightForm;

class WeightRestController extends AbstractRestfulController
{

    protected $em;
    
    public function __construct() {
    }

    public function getList()
    {
    }

    public function get($id)
    {
    }

    public function create($data)
    {
        $id = (int) $data['id'];
        $entityName = $data['entity'];
        $entity = $this->getEntityManager()->find("Flashcard\Entity\\{$entityName}", $id);
        if (!$entity || !$id ) {
            $data = 'false';
        }
        else {
            $form = new WeightForm();
            $form->setData($data);
            if ($form->isValid()) {
                $entity->setWeight($data['weight']);
                $this->getEntityManager()->persist($entity);
                try {
                    $this->getEntityManager()->flush();
                    $entity = $this->getEntityManager()->find("Flashcard\Entity\\{$entityName}", $id);
                    $data = $entity->getWeight();
                }
                catch (Exception $e) {
                    $data = 'false';
                }                
            }
        }
        
        return new JsonModel(array(
            'data' => $data,
        ));
    }

    public function update($id, $data)
    {
    }

    public function delete($id)
    {
    }

    public function getStudyData()
    {
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

}
