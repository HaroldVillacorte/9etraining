<?php

namespace Flashcard\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Escaper\Escaper;

class StudyRestController extends AbstractRestfulController
{
    protected $em;
    protected $escaper;

    public function __construct() {
        $this->escaper = new Escaper();
    }

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

        $domainQuery = 'SELECT u FROM Flashcard\Entity\Domain u ORDER BY u.weight ASC';
        $domains = $this->getEntityManager()->createQuery($domainQuery)->getResult();

        $categoriesQuery = 'SELECT u FROM Flashcard\Entity\Category u JOIN Flashcard\Entity\Domain d'
                . ' WHERE d = u.domain ORDER BY d.weight, u.weight ASC';
        $categories = $this->getEntityManager()->createQuery($categoriesQuery )->getResult();

        $questionsQuery = 'SELECT u FROM Flashcard\Entity\Question u JOIN Flashcard\Entity\Categgory c'
                . ' WHERE c = u.category JOIN Flashcard\Entity\Domain d WHERE d = c.domain'
                . ' ORDER BY d.weight, c.weight, u.weight ASC';
        $questions = $this->getEntityManager()->createQuery($questionsQuery )->getResult();

        // Set the domains.
        $i = 0;
        foreach ($domains as $domain){
            $data['domains'][$i]['id'] = (int) $domain->getId();
            $data['domains'][$i]['name'] = $this->escaper->escapeHtml($domain->getName());
            $i++;
        }

        // Set the categories.
        $ii = 0;
        foreach ($categories as $category){
            $data['categories'][$ii]['id'] = (int) $category->getId();
            $data['categories'][$ii]['name'] = $this->escaper->escapeHtml($category->getName());
            $data['categories'][$ii]['domainId'] = $this->escaper->escapeHtml($category->getDomain()->getId());
            $ii++;
        }

        $iii = 0;
        foreach ($questions as $question){
            $data['questions'][$iii]['id'] = (int) $question->getId();
            $data['questions'][$iii]['question'] = $this->escaper->escapeHtml($question->getQuestion());
            $data['questions'][$iii]['answer'] = $this->escaper->escapeHtml($question->getAnswer());
            $data['questions'][$iii]['note'] = $this->escaper->escapeHtml($question->getNote());
            $data['questions'][$iii]['categoryId'] = (int) $question->getCategory()->getId();
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
