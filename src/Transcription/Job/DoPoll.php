<?php
namespace Services\Transcription\Job;

use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class DoPoll extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');
        $logger = $this->get('Omeka\Logger');

        $transcriptionIds = $apiManager->search(
            'services_transcription_transcriptions',
            ['project_id' => $this->getProject()->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($transcriptionIds, 100) as $transcriptionIdsChunk) {
            // Iterate transcriptions.
            foreach ($transcriptionIdsChunk as $transcriptionId) {
                $transcription = $entityManager
                    ->getRepository(ServicesTranscriptionTranscription::class)
                    ->find($transcriptionId);
                $page = $transcription->getPage();

                $logger->notice(sprintf(
                    'Polling transcription for media ID %s page ID %s',
                    $page->getMedia()->getId(),
                    $page->getId()
                ));

                if (null === $transcription->getJobId()) {
                    $logger->notice('Not possible when transcription not initiated');
                    continue;
                }
                if (in_array($transcription->getJobState(), ['completed', 'retry', 'failed', 'cancelled'])) {
                    $logger->notice(sprintf(
                        'Already polled with status "%s"',
                        $transcription->getJobState()
                    ));
                    continue;
                }

                // Poll Mino for the transcription.
                $client = $this->get('Omeka\HttpClient')
                    ->setMethod('GET')
                    ->setUri(sprintf('https://mino.tropy.org/transcription/%s', $transcription->getJobId()));
                $headers = $client->getRequest()->getHeaders();
                $headers->addHeaders([
                    'Authorization' => sprintf('Bearer %s', $this->getProject()->getAccessToken()),
                ]);
                $response = $client->send();
                if (!$response->isSuccess()) {
                    throw new \Exception(sprintf(
                        'Poll failed with status "%s": %s',
                        $response->getStatusCode(),
                        $response->getContent()
                    ));
                }
                $content = json_decode($response->getContent(), true);

                $transcription->setJobState($content['state']);
                $transcription->setText($content['output']['text']);
                $transcription->setData($content['output']['alto']);
                $logger->notice(sprintf(
                    'Poll done with status "%s"',
                    $content['state']
                ));
            }
            $entityManager->flush();
        }
    }
}
