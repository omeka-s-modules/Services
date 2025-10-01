<?php
namespace Services;

return [
    'services_module' => [
        'transcription' => [
            'media_preprocessers' => [
                'factories' => [
                    'file' => Transcription\Service\Preprocesser\Media\FileFactory::class,
                ],
            ],
            'file_preprocessers' => [
                'factories' => [
                    'application/pdf' => Transcription\Service\Preprocesser\File\MultipageSplitFactory::class,
                    'image/tiff' => Transcription\Service\Preprocesser\File\MultipageSplitFactory::class,
                ],
            ],
        ],
    ],
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
        'factories' => [
            'Services\Transcription\MediaPreprocesserManager' => Transcription\Service\Preprocesser\MediaPreprocesserManagerFactory::class,
            'Services\Transcription\FilePreprocesserManager' => Transcription\Service\Preprocesser\FilePreprocesserManagerFactory::class,
        ],
    ],
    'api_adapters' => [
        'invokables' => [
            'services_transcription_projects' => Transcription\Api\Adapter\ProjectAdapter::class,
            'services_transcription_pages' => Transcription\Api\Adapter\PageAdapter::class,
            'services_transcription_transcriptions' => Transcription\Api\Adapter\TranscriptionAdapter::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            'Services\Services\Controller\Admin\Index' => Services\Service\Controller\Admin\IndexControllerFactory::class,
            'Services\Transcription\Controller\Admin\Index' => Transcription\Service\Controller\Admin\IndexControllerFactory::class,
            'Services\Transcription\Controller\Admin\Project' => Transcription\Service\Controller\Admin\ProjectControllerFactory::class,
            'Services\Transcription\Controller\Admin\Item' => Transcription\Service\Controller\Admin\ItemControllerFactory::class,
            'Services\Transcription\Controller\Admin\Media' => Transcription\Service\Controller\Admin\MediaControllerFactory::class,
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
            'Services\Transcription\Form\DoPreprocessForm' => Transcription\Service\Form\DoPreprocessFormFactory::class,
            // 'Transcription\Services\Form\DoTranscribeForm' => Transcription\Service\Form\DoTranscribeFormFactory::class,
            // 'Transcription\Services\Form\DoFetchForm' => Transcription\Service\Form\DoFetchFormFactory::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Services', // @translate
                'route' => 'admin/services',
                'resource' => 'Services\Services\Controller\Admin\Index',
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
                            [
                                'route' => 'admin/services/transcription-project-item',
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
                                '__NAMESPACE__' => 'Services\Services\Controller\Admin',
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
                                    'route' => '/transcription/project/:project-id[/:action]',
                                    'constraints' => [
                                        'project-id' => '\d+',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'project',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                            'transcription-project-item-id' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/transcription/project/:project-id/item/:item-id[/:action]',
                                    'constraints' => [
                                        'project-id' => '\d+',
                                        'item-id' => '\d+',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'item',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                            'transcription-project-media-id' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/transcription/project/:project-id/item/:item-id/media/:media-id[/:action]',
                                    'constraints' => [
                                        'project-id' => '\d+',
                                        'item-id' => '\d+',
                                        'media-id' => '\d+',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'media',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                            'transcription-project-page-id' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/transcription/project/:project-id/item/:item-id/media/:media-id/page/:page-id[/:action]',
                                    'constraints' => [
                                        'project-id' => '\d+',
                                        'item-id' => '\d+',
                                        'media-id' => '\d+',
                                        'page-id' => '\d+',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Services\Transcription\Controller\Admin',
                                        'controller' => 'page',
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
