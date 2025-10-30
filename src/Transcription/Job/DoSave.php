<?php
namespace Services\Transcription\Job;

use Services\Transcription\Entity\ServicesTranscriptionTranscription;


class DoSave extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');

        // Get all page IDs in this project and iterate them.
        $transcriptionIds = $apiManager->search(
            'services_transcription_transcriptions',
            ['project_id' => $this->getProject()->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($transcriptionIds, 100) as $transcriptionIdsChunk) {
            foreach ($transcriptionIdsChunk as $transcriptionId) {

                // Get the transcription.
                $transcription = $entityManager
                    ->getRepository(ServicesTranscriptionTranscription::class)
                    ->find($transcriptionId);

                // @todo: save transcriptions to target resource and property
            }
        }
    }
}
