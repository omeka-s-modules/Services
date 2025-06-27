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
        $owner = $this->owner();
        return [
            'o:owner' => $owner ? $owner->getReference() : null,
            'o:label' => $this->label(),
            'o:query' => $this->queryuery(),
        ];
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

    public function owner()
    {
        return $this->getAdapter('users')->getRepresentation($this->resource->getOwner());
    }

    public function label()
    {
        return $this->resource->getLabel();
    }

    public function query()
    {
        return $this->resource->getQuery();
    }
}
