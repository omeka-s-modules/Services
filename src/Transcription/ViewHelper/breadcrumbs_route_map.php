<?php
return [
    'admin/services/transcription-project' => [
        'breadcrumbs' => [],
        'text' => 'Projects', // @translate
        'params' => [],
    ],
    'admin/services/transcription-project-id' => [
        'breadcrumbs' => ['admin/services/transcription-project'],
        'text' => 'Project', // @translate
        'params' => ['project-id'],
    ],
    'admin/services/transcription-project-item-id' => [
        'breadcrumbs' => ['admin/services/transcription-project', 'admin/services/transcription-project-id'],
        'text' => 'Item', // @translate
        'params' => ['project-id', 'item-id'],
    ],
    'admin/services/transcription-project-media-id' => [
        'breadcrumbs' => ['admin/services/transcription-project', 'admin/services/transcription-project-id', 'admin/services/transcription-project-item-id'],
        'text' => 'Media', // @translate
        'params' => ['project-id', 'item-id'],
    ],
];
