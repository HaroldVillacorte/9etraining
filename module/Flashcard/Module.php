<?php

namespace Flashcard;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'studyLink' => function ($sm) {
                    $sm = $sm->getServiceLocator();
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $view_helper = new \Flashcard\View\Helper\StudyLinkHelper();
                    $view_helper->setEntityManager($em);
                    return $view_helper;
                }
            ),
        );
    }

}
