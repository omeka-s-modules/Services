<?php
namespace Services\Transcription\Job;

use Omeka\Job\AbstractJob;

abstract class AbstractTranscriptionJob extends AbstractJob
{
    protected $project;

    /**
     * Get a named service. Proxy to $this->getServiceLocator().
     */
    public function get(string $serviceName)
    {
        return $this->getServiceLocator()->get($serviceName);
    }

    /**
     * Get the project entity.
     */
    public function getProject()
    {
        if (null === $this->project) {
            $this->project = $this->get('Omeka\EntityManager')
                ->getRepository('Services\Transcription\Entity\ServicesTranscriptionProject')
                ->find($this->getArg('project_id'));
        }
        return $this->project;
    }
}
