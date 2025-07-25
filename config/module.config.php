<?php
namespace Services;

use Laminas\Router\Http;

return [
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Transcription/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            sprintf('%s/../view', __DIR__),
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => sprintf('%s/../language', __DIR__),
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [],
    ],
    'api_adapters' => [
        'invokables' => [
            'services_transcription_project' => Transcription\Api\Adapter\ProjectAdapter::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            'Services\Controller\Admin\Index' => Service\Controller\Admin\IndexControllerFactory::class,
            'Services\Transcription\Controller\Admin\Index' => Transcription\Service\Controller\Admin\IndexControllerFactory::class,
            'Services\Transcription\Controller\Admin\Project' => Transcription\Service\Controller\Admin\ProjectControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'servicesTranscription' => Transcription\Service\ControllerPlugin\ServicesTranscriptionFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'servicesTranscription' => Transcription\Service\ViewHelper\ServicesTranscriptionFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'Services\Transcription\Form\ProjectForm' => Transcription\Service\Form\ProjectFormFactory::class,
            // 'Transcription\Services\Form\DoPrepareForm' => Transcription\Service\Form\DoPrepareFormFactory::class,
            // 'Transcription\Services\Form\DoTranscribeForm' => Transcription\Service\Form\DoTranscribeFormFactory::class,
            // 'Transcription\Services\Form\DoFetchForm' => Transcription\Service\Form\DoFetchFormFactory::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Services', // @translate
                'route' => 'admin/services',
                'resource' => 'Services\Controller\Admin\Index',
                'pages' => [
                    [
                        'label' => 'Transcription', // @translate
                        'route' => 'admin/services/transcription-project',
                        'resource' => 'Services\Transcription\Controller\Admin\Index',
                        'action' => 'browse',
                        'pages' => [
                            [
                                'route' => 'admin/services/transcription-project-id',
                                'visible' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'services' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/services',
                            'defaults' => [
                                '__NAMESPACE__' => 'Services\Controller\Admin',
                                'controller' => 'index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'transcription' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/transcription[/:action]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'index',
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'transcription-project' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/transcription/project[/:action]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'project',
                                        'action' => 'browse',
                                    ],
                                ],
                            ],
                            'transcription-project-id' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/transcription/project/:id[/:action]',
                                    'constraints' => [
                                        'id' => '\d+',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'project',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
