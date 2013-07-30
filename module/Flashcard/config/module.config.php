<?php

namespace Flashcard;

return array(
    'controllers' => array(
        'invokables' => array(
            'Flashcard\Controller\Category' => 'Flashcard\Controller\CategoryController',
            'Flashcard\Controller\Question' => 'Flashcard\Controller\QuestionController',
            'Flashcard\Controller\Study' => 'Flashcard\Controller\StudyController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'category' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/category[/:action][/:id]',
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
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'flashcard' => __DIR__ . '/../view',
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