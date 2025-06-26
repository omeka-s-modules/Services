<?php
namespace Services;

use Laminas\Router\Http;

return [
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
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
        'invokables' => [],
    ],
    'controllers' => [
        'factories' => [
            'Services\Controller\Admin\Index' => Service\Controller\Admin\IndexControllerFactory::class,
            'Services\Controller\Admin\Transcription' => Service\Controller\Admin\TranscriptionControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'Services\Form\TranscriptionForm' => Service\Form\TranscriptionFormFactory::class,
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
                        'label' => 'Transcriptions', // @translate
                        'route' => 'admin/services/transcription',
                        'resource' => 'Services\Controller\Admin\Transcription',
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
                                    'route' => '/transcription[/:action]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'transcription',
                                        'action' => 'browse',
                                    ],
                                ],
                            ],
                            'transcription-id' => [
                                'type' => Http\Segment::class,
                                'options' => [
                                    'route' => '/transcription/:id[/:action]',
                                    'constraints' => [
                                        'id' => '\d+',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'transcription',
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
