<?php
namespace Services\Transcription\Job;

use Exception;
use Services\Services\Mino\Mino;
use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class DoTranscribe extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');
        $fileStore = $this->get('Omeka\File\Store');
        $logger = $this->get('Omeka\Logger');
        $mino = $this->get('Services\Mino');

        $action = $this->getArg('action');

        if ('transcribe_all' === $action) {
            $logger->notice('Deleting all transcriptions in project ...');
            $queryBuilder = $entityManager->createQueryBuilder();
            $query = $queryBuilder
                ->delete('Services\Transcription\Entity\ServicesTranscriptionTranscription', 't')
                ->where($queryBuilder->expr()->eq('t.project', $this->getProject()->getId()))
                ->getQuery()
                ->execute();
        }

        if ('transcribe_failed' === $action) {
            $logger->notice('Deleting failed transcriptions in project ...');
            $queryBuilder = $entityManager->createQueryBuilder();
            $query = $queryBuilder
                ->delete('Services\Transcription\Entity\ServicesTranscriptionTranscription', 't')
                ->where($queryBuilder->expr()->eq('t.project', $this->getProject()->getId()))
                ->andWhere($queryBuilder->expr()->in('t.jobState', Mino::FAILED_JOB_STATES))
                ->getQuery()
                ->execute();
        }

        // Get all page IDs in this project and iterate them.
        $pageIds = $apiManager->search(
            'services_transcription_pages',
            ['project_id' => $this->getProject()->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($pageIds, 100) as $pageIdsChunk) {
            foreach ($pageIdsChunk as $pageId) {

                // Get the page.
                $page = $entityManager
                    ->getRepository(ServicesTranscriptionPage::class)
                    ->find($pageId);

                $logger->notice(sprintf(
                    'Requesting transcription for media %s page %s ...',
                    $page->getMedia()->getId(),
                    $page->getId()
                ));

                // Get the page's transcription, if any.
                $transcription = $entityManager
                    ->getRepository('Services\Transcription\Entity\ServicesTranscriptionTranscription')
                    ->findOneBy(['project' => $this->getProject(), 'page' => $page]);

                if ($transcription) {
                    // The transcription exists. If needed, poll Mino for
                    // transcription updates.
                    if ('completed' === $transcription->getJobState()) {
                        $logger->notice('Transcription already completed');
                        continue;
                    }
                    try {
                        $content = $mino->poll(
                            $transcription->getJobId(),
                            $this->getProject()->getAccessToken()
                        );
                    } catch (Exception $e) {
                        $logger->err($e->getMessage());
                        continue;
                    }

                    $transcription->setJobState($content['state']);
                    $transcription->setText($content['output']['text']);
                    $transcription->setData($content['output']['alto']);

                } else {
                    // The transcription does not exist. Submit upload and
                    // transcription requests to Mino.
                    $imageUrl = $fileStore->getUri($page->getStoragePath());
                    try {
                        $image = $mino->upload(
                            $imageUrl,
                            $this->getProject()->getAccessToken()
                        );
                        $job = $mino->transcribe(
                            $image,
                            $this->getProject()->getModelId(),
                            $this->getProject()->getAccessToken()
                        );
                    } catch (Exception $e) {
                        $logger->err($e->getMessage());
                        continue;
                    }

                    // Create the transcription record.
                    $transcription = new ServicesTranscriptionTranscription;
                    $transcription->setProject($this->getProject());
                    $transcription->setPage($page);
                    $transcription->setJobId($job['id']);
                    $transcription->setJobState($job['state']);

                    $entityManager->persist($transcription);

                    // Sleep for 2 seconds to account for Mino's rate limit.
                    sleep(2);
                }
            }
            $entityManager->flush();
        }
    }
}
