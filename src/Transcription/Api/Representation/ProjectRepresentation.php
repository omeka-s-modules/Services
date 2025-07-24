<?php
namespace Services\Transcription\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class ProjectRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-services:TranscriptionProject';
    }

    public function getJsonLd()
    {
        $owner = $this->owner();
        $modified = $this->modified();
        return [
            'o:owner' => $owner ? $owner->getReference() : null,
            'o:label' => $this->label(),
            'o-module-services:model-id' => $this->modelId(),
            'o-module-services:access-token' => $this->accessToken(),
            'o:query' => $this->query(),
            'o:created' => $this->getDateTime($this->created()),
            'o:modified' => $modified ? $this->getDateTime($modified) : null,
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
                'id' => $this->id(),
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

    public function modelId()
    {
        return $this->resource->getModelId();
    }

    public function accessToken()
    {
        return $this->resource->getAccessToken();
    }

    public function query()
    {
        return $this->resource->getQuery();
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function modified()
    {
        return $this->resource->getModified();
    }
}
