<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class StudyRestController extends AbstractRestfulController
{
    protected $em;

    public function getList()
    {
    }

    public function get($id)
    {
    }

    public function create($data)
    {
        header("Access-Control-Allow-Origin: *");

        $username = (string) $data['username'];
        $apiKey = (string) $data['apiKey'];

        $user = $this->getEntityManager()->getRepository('ZfcUserExtend\Entity\User')
            ->findOneBy(array('username' => $username));

        if (!$user)
        {
            return new JsonModel(array(
                'data' => 'user',
            ));
        }
        elseif ($user->getApiKey()->getKeyValue() !== $apiKey) {
            return new JsonModel(array(
                'data' => 'key',
            ));
        }
        else {
            $data = $this->getStudyData();

            return new JsonModel(array(
                'data' => $data,
            ));
        }
    }

    public function update($id, $data)
    {
    }

    public function delete($id)
    {
    }

    public function getStudyData()
    {
        $data = array();

        $domains = $this->getEntityManager()->getRepository('Flashcard\Entity\Domain')
            ->findAll();

        $categories = $this->getEntityManager()->getRepository('Flashcard\Entity\Category')
            ->findAll();

        $questions = $this->getEntityManager()->getRepository('Flashcard\Entity\Question')
            ->findAll();

        // Set the domains.
        $i = 0;
        foreach ($domains as $domain){
            $data['domains'][$i]['id'] = $domain->getId();
            $data['domains'][$i]['name'] = $domain->getName();
            $i++;
        }

        // Set the categories.
        $ii = 0;
        foreach ($categories as $category){
            $data['categories'][$ii]['id'] = $category->getId();
            $data['categories'][$ii]['name'] = $category->getName();
            $data['categories'][$ii]['domainId'] = $category->getDomain()->getId();
            $ii++;
        }

        $iii = 0;
        foreach ($questions as $question){
            $data['questions'][$iii]['id'] = $question->getId();
            $data['questions'][$iii]['question'] = $question->getQuestion();
            $data['questions'][$iii]['answer'] = addslashes($question->getAnswer());
            $data['questions'][$iii]['note'] = $question->getNote();
            $data['questions'][$iii]['categoryId'] = $question->getCategory()->getId();
            $iii++;
        }

        return $data;
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
