<?php
namespace Services\Transcription\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class TranscriptionRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-services:TranscriptionTranscription';
    }

    public function getJsonLd()
    {
        $modified = $this->modified();
        return [
            'o-module-services:project' => $this->project()->getReference(),
            'o-module-services:page' => $this->page()->getReference(),
            'o-module-services:job-state' => $this->jobState(),
            'o-module-services:job-id' => $this->jobId(),
            'o-module-services:text' => $this->text(),
            'o-module-services:data' => $this->data(),
            'o:created' => $this->getDateTime($this->created()),
            'o:modified' => $modified ? $this->getDateTime($modified) : null,
        ];
    }

    public function project()
    {
        return $this->getAdapter('services_transcription_projects')->getRepresentation($this->resource->getProject());
    }

    public function page()
    {
        return $this->getAdapter('services_transcription_pages')->getRepresentation($this->resource->getPage());
    }

    public function jobState()
    {
        return $this->resource->getJobState();
    }

    public function jobId()
    {
        return $this->resource->getJobId();
    }

    public function text()
    {
        return $this->resource->getText();
    }

    public function data()
    {
        return $this->resource->getData();
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
