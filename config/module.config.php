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
            'Transcription\Services\Form\TranscriptionForm' => Transcription\Service\Form\TranscriptionFormFactory::class,
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
                        'route' => 'admin/services/transcription',
                        'resource' => 'Services\Transcription\Controller\Admin\Index',
                        'action' => 'browse',
                        'pages' => [
                            [
                                'route' => 'admin/services/transcription/id',
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
                                'type' => Http\Segment::class,
                                'options' => [
                                    'route' => '/transcription/:action',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'id' => [
                                        'type' => Http\Segment::class,
                                        'options' => [
                                            'route' => '/:id',
                                            'constraints' => [
                                                'id' => '\d+',
                                            ],
                                            'defaults' => [],
                                        ],
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
