<?php

namespace Flashcard\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StudyLinkHelper extends AbstractHelper
{

    protected $em;

    public function __invoke()
    {
        $domains = $this
            ->getEntityManager()
            ->getRepository('Flashcard\Entity\Domain')
            ->findAll();

        $url = $this->view->plugin('url');

        $links = '';

        foreach ($domains as $domain)
        {
            $links .= '<li class="has-dropdown">';
            $links .= '<a href="#">' . $domain->getName() . '</a>';
            $links .= '<ul class="dropdown">';
            foreach ($domain->getCategories() as $category)
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
