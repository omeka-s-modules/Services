<?php
namespace Services\Transcription\Job;

use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;
use Services\Transcription\Entity\ServicesTranscriptionPage;

class DoTranscribe extends AbstractJob
{
    protected $project;

    public function perform()
    {
        $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
        $apiManager = $this->getServiceLocator()->get('Omeka\ApiManager');
        $logger = $this->getServiceLocator()->get('Omeka\Logger');

        // Get the project.
        $project = $entityManager
            ->getRepository('Services\Transcription\Entity\ServicesTranscriptionProject')
            ->find($this->getArg('project_id'));
    }
}
