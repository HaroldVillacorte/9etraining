<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'bjyauthorize' => array(
        // default role for unauthenticated users
        'default_role'          => 'guest',

        // default role for authenticated users (if using the
        // 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider' identity provider)
        'authenticated_role'    => 'user',

        // identity provider service name
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        // Role providers to be used to load all available roles into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'role_providers'        => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array(),
                'user'  => array(),
            ),
        ),

        // Resource providers to be used to load all available resources into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'resource_providers'    => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'userLink' => array(),
                'adminLink' => array(),
                'superuserLink' => array(),
            ),
        ),

        // Rule providers to be used to load all available rules into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'rule_providers'        => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"
                    array(array('user'), 'userLink', 'view'),
                    array(array('admin'), 'adminLink', 'view'),
                    array(array('superuser'), 'superuserLink', 'view'),
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                ),
            ),
        ),

        // Guard listeners to be attached to the application event manager
        'guards'                => array(
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'zfcuser', 'roles' => array('guest', 'user')),
                array('route' => 'zfcuser/logout', 'roles' => array('guest', 'user')),
                array('route' => 'zfcuser/login', 'roles' => array('guest', 'user')),
                array('route' => 'zfcuser/register', 'roles' => array('guest', 'user')),
                array('route' => 'role', 'roles' => array('admin')),
                array('route' => 'useradmin', 'roles' => array('admin')),
                array('route' => 'home', 'roles' => array('guest', 'user')),

                // ZF2FileUploadExamples
                array('route' => 'fileupload', 'roles' => array('user')),

                // Variable.
                array('route' => 'variable', 'roles' => array('user')),

                // Team-contact.
                array('route' => 'team-contact', 'roles' => array('guest', 'user')),

                // Falshcard module.
                array('route' => 'domain', 'roles' => array('admin')),
                array('route' => 'domainIndex', 'roles' => array('admin')),
                array('route' => 'category', 'roles' => array('admin')),
                array('route' => 'categoryIndex', 'roles' => array('admin')),
                array('route' => 'question', 'roles' => array('admin')),
                array('route' => 'questionIndex', 'roles' => array('admin')),
                array('route' => 'questionByCategory', 'roles' => array('admin')),
                array('route' => 'study', 'roles' => array('user')),
                array('route' => 'study-rest', 'roles' => array('guest')),
                array('route' => 'weight-rest', 'roles' => array('admin')),
            ),
        ),

        // strategy service name for the strategy listener to be used when permission-related errors are detected
        'unauthorized_strategy' => 'BjyAuthorize\View\UnauthorizedStrategy',

        // Template name for the unauthorized strategy
        'template'              => 'error/403',
    ),

    'service_manager' => array(
        'factories' => array(
            'BjyAuthorize\Config'                   => 'BjyAuthorize\Service\ConfigServiceFactory',
            'BjyAuthorize\Guards'                   => 'BjyAuthorize\Service\GuardsServiceFactory',
            'BjyAuthorize\RoleProviders'            => 'BjyAuthorize\Service\RoleProvidersServiceFactory',
            'BjyAuthorize\ResourceProviders'        => 'BjyAuthorize\Service\ResourceProvidersServiceFactory',
            'BjyAuthorize\RuleProviders'            => 'BjyAuthorize\Service\RuleProvidersServiceFactory',
            'BjyAuthorize\Guard\Controller'         => 'BjyAuthorize\Service\ControllerGuardServiceFactory',
            'BjyAuthorize\Guard\Route'              => 'BjyAuthorize\Service\RouteGuardServiceFactory',
            'BjyAuthorize\Provider\Role\Config'     => 'BjyAuthorize\Service\ConfigRoleProviderServiceFactory',
            'BjyAuthorize\Provider\Role\ZendDb'     => 'BjyAuthorize\Service\ZendDbRoleProviderServiceFactory',
            'BjyAuthorize\Provider\Resource\Config' => 'BjyAuthorize\Service\ConfigResourceProviderServiceFactory',
            'BjyAuthorize\Service\Authorize'        => 'BjyAuthorize\Service\AuthorizeFactory',
            'BjyAuthorize\Provider\Identity\ProviderInterface'
                => 'BjyAuthorize\Service\IdentityProviderServiceFactory',
            'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider'
                => 'BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory',
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider'
                => 'BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory',
            'BjyAuthorize\Collector\RoleCollector'  => 'BjyAuthorize\Service\RoleCollectorServiceFactory',
            'BjyAuthorize\Provider\Identity\ZfcUserZendDb'
                => 'BjyAuthorize\Service\ZfcUserZendDbIdentityProviderServiceFactory',
            'BjyAuthorize\View\UnauthorizedStrategy'
                => 'BjyAuthorize\Service\UnauthorizedStrategyServiceFactory',
        ),
        'invokables'  => array(
            'BjyAuthorize\View\RedirectionStrategy' => 'BjyAuthorize\View\RedirectionStrategy',
        ),
        'aliases'     => array(
            'bjyauthorize_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ),
        'initializers' => array(
            'BjyAuthorize\Service\AuthorizeAwareServiceInitializer'
                => 'BjyAuthorize\Service\AuthorizeAwareServiceInitializer'
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'error/403' => 'vendor/bjyoungblood/bjy-authorize/view/error/403.phtml',
            'zend-developer-tools/toolbar/bjy-authorize-role'
                => 'vendor/bjyoungblood/bjy-authorize/view/zend-developer-tools/toolbar/bjy-authorize-role.phtml',
        ),
    ),

    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'bjy_authorize_role_collector' => 'BjyAuthorize\\Collector\\RoleCollector',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'bjy_authorize_role_collector' => 'zend-developer-tools/toolbar/bjy-authorize-role',
            ),
        ),
    ),
);
