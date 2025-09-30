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
        $preprocessJob = $this->preprocessJob();
        $transcribeJob = $this->transcribeJob();
        $fetchJob = $this->fetchJob();
        return [
            'o:owner' => $owner ? $owner->getReference() : null,
            'o:label' => $this->label(),
            'o-module-services:model-id' => $this->modelId(),
            'o-module-services:access-token' => $this->accessToken(),
            'o-module-services:preprocess-job' => $preprocessJob ? $preprocessJob->getReference() : null,
            'o-module-services:transcribe-job' => $transcribeJob ? $transcribeJob->getReference() : null,
            'o-module-services:fetch-job' => $fetchJob ? $fetchJob->getReference() : null,
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
                'project-id' => $this->id(),
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

    public function preprocessJob()
    {
        return $this->getAdapter('jobs')->getRepresentation($this->resource->getPreprocessJob());
    }

    public function transcribeJob()
    {
        return $this->getAdapter('jobs')->getRepresentation($this->resource->getTranscribeJob());
    }

    public function fetchJob()
    {
        return $this->getAdapter('jobs')->getRepresentation($this->resource->getFetchJob());
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function modified()
    {
        return $this->resource->getModified();
    }

    public function itemCount()
    {
        $query = [
            'limit' => 0,
            'services_transcription_project_id' => $this->id(),
        ];
        return $this->getServiceLocator()
            ->get('Omeka\ApiManager')
            ->search('items', $query)
            ->getTotalResults();
    }

    public function mediaCount()
    {
        $query = [
            'limit' => 0,
            'services_transcription_project_id' => $this->id(),
        ];
        return $this->getServiceLocator()
            ->get('Omeka\ApiManager')
            ->search('media', $query)
            ->getTotalResults();
    }

    public function pageCount()
    {
        $query = [
            'limit' => 0,
            'services_transcription_project_id' => $this->id(),
        ];
        return $this->getServiceLocator()
            ->get('Omeka\ApiManager')
            ->search('services_transcription_pages', $query)
            ->getTotalResults();
    }

    public function transcriptionCount()
    {
        $query = [
            'limit' => 0,
            'services_transcription_project_id' => $this->id(),
        ];
        return $this->getServiceLocator()
            ->get('Omeka\ApiManager')
            ->search('services_transcription_transcriptions', $query)
            ->getTotalResults();
    }
}
