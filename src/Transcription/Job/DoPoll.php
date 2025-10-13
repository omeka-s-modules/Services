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
        $fileStore = $this->get('Omeka\File\Store');
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
            }
            $entityManager->flush();
        }
    }
}
