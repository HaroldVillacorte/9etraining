<?php

namespace Flashcard;

return array(
    'controllers' => array(
        'invokables' => array(
            'Flashcard\Controller\Domain' => 'Flashcard\Controller\DomainController',
            'Flashcard\Controller\Category' => 'Flashcard\Controller\CategoryController',
            'Flashcard\Controller\Question' => 'Flashcard\Controller\QuestionController',
            'Flashcard\Controller\Study' => 'Flashcard\Controller\StudyController',            
            'Flashcard\Controller\StudyRest' => 'Flashcard\Controller\StudyRestController',
            'Flashcard\Controller\WeightRest' => 'Flashcard\Controller\WeightRestController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'domain' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/domain[/:action][/:id][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Domain',
                        'action'     => 'index',
                    ),
                ),
            ),
            'domainIndex' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/domain[/:page]',
                    'constraints' => array(
                        'page'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Domain',
                        'action'     => 'index',
                        'page' => null,
                    ),
                ),
            ),
            'category' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/category[/:action][/:id][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Category',
                        'action'     => 'index',
                    ),
                ),
            ),
            'categoryIndex' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/category[/:page]',
                    'constraints' => array(
                        'page'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Category',
                        'action'     => 'index',
                        'page' => null,
                    ),
                ),
            ),
            'question' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/question[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Question',
                        'action'     => 'index',
                    ),
                ),
            ),
            'questionIndex' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/question[/:page]',
                    'constraints' => array(
                        'page'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Question',
                        'action'     => 'index',
                        'page' => null,
                    ),
                ),
            ),
            'questionByCategory' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/questionByCategory[/:id][/:page]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                        'page'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Question',
                        'action'     => 'byCategory',
                        'id' => null,
                        'page' => null,
                    ),
                ),
            ),
            'study' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/study[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\Study',
                        'action'     => 'index',
                    ),
                ),
            ),
            'study-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/study-rest[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\StudyRest',
                    ),
                ),
            ),
            'weight-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/weight-rest[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Flashcard\Controller\WeightRest',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'flashcard' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ),
            ),
        ),
    ),
);