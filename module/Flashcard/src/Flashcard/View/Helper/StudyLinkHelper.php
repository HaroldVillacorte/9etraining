<?php

namespace Flashcard\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StudyLinkHelper extends AbstractHelper
{

    protected $em;

    public function __invoke()
    {
        $domainQuery = 'SELECT u FROM Flashcard\Entity\Domain u ORDER BY u.weight ASC';
        $domains = $this->getEntityManager()->createQuery($domainQuery)->getResult();

        $url = $this->view->plugin('url');

        $links = '';

        foreach ($domains as $domain)
        {
            $domainId = $domain->getId();
            $categoriesQuery = "SELECT u FROM Flashcard\Entity\Category u WHERE u.domain = {$domainId} ORDER BY u.weight ASC";
            $catgories = $this->getEntityManager()->createQuery($categoriesQuery)->getResult();

            $links .= '<li class="has-dropdown">';
            $links .= '<a href="#">' . $domain->getName() . '</a>';
            $links .= '<ul class="dropdown">';
            foreach ($catgories as $category)
            {
                $links .= '<li><a href="' .
                $url('study', array('action' => 'category', 'id' => $category->getId())) .
                '">' . $category->getName() . '</a></li>';
                $links .= '<li class="divider"></li>';
            }
            $links .= '</ul>';
            $links .= '</li>';


        }

        return $links;
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

}
