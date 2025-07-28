<?php
namespace Services\Transcription\Job;

use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;
use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class DoTranscribe extends AbstractJob
{
    protected $project;

    public function perform()
    {
        $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
        $apiManager = $this->getServiceLocator()->get('Omeka\ApiManager');
        $httpClient = $this->getServiceLocator()->get('Omeka\HttpClient');
        $logger = $this->getServiceLocator()->get('Omeka\Logger');

        // Get the project.
        $project = $entityManager
            ->getRepository('Services\Transcription\Entity\ServicesTranscriptionProject')
            ->find($this->getArg('project_id'));

        $pageIds = $apiManager->search(
            'services_transcription_pages',
            ['services_transcription_project_id' => $project->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($pageIds, 100) as $pageIdsChunk) {
            // Iterate pages.
            foreach ($pageIdsChunk as $pageId) {
                $page = $entityManager
                    ->getRepository(ServicesTranscriptionPage::class)
                    ->find($pageId);
                $transcription = $entityManager
                    ->getRepository('Services\Transcription\Entity\ServicesTranscriptionTranscription')
                    ->findBy(['project' => $project, 'page' => $page]);
                if (!$transcription) {
                    // The transcription does not exist. Create it.
                    $transcription = new ServicesTranscriptionTranscription;
                    $transcription->setProject($project);
                    $transcription->setPage($page);
                    $entityManager->persist($transcription);
                }

                // @todo: upload page https://mino.tropy.org/upload

                // @todo: initiate transcription https://mino.tropy.org/transcription
            }
            $entityManager->flush();
        }
    }
}
