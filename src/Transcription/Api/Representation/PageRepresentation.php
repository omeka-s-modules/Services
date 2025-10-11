<?php
namespace Services\Transcription\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class PageRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-services:TranscriptionPage';
    }

    public function getJsonLd()
    {
        $modified = $this->modified();
        return [
            'o:item' => $this->item()->getReference(),
            'o:media' => $this->media()->getReference(),
            'o-module-services:storage_path' => $this->storagePath(),
            'o:position' => $this->position(),
            'o:created' => $this->getDateTime($this->created()),
            'o:modified' => $modified ? $this->getDateTime($modified) : null,
        ];
    }

    public function item()
    {
        return $this->getAdapter('items')->getRepresentation($this->resource->getItem());
    }

    public function media()
    {
        return $this->getAdapter('media')->getRepresentation($this->resource->getMedia());
    }

    public function storagePath()
    {
        return $this->resource->getStoragePath();
    }

    public function position()
    {
        return $this->resource->getPosition();
    }

    public function imageUrl(): string
    {
        $store = $this->getServiceLocator()->get('Omeka\File\Store');
        return $store->getUri($this->storagePath());
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
