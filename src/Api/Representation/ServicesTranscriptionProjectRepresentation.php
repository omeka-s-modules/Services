<?php
namespace Services\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class ServicesTranscriptionProjectRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-services:TranscriptionProject';
    }

    public function getJsonLd()
    {
        return [];
    }

    public function adminUrl($action = null, $canonical = false)
    {
        $url = $this->getViewHelper('Url');
        return $url(
            'admin/services/transcription-project-id',
            [
                'controller' => 'transcription',
                'action' => $action,
                'transcription-project-id' => $this->id(),
            ],
            ['force_canonical' => $canonical]
        );
    }
}
