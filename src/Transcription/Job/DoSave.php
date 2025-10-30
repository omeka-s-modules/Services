<?php
namespace Services\Transcription\Job;

use Omeka\Entity\Item;
use Services\Services\Mino\Mino;
use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class DoSave extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');
        $logger = $this->get('Omeka\Logger');

        // Get the item IDs.
        $itemIds = $apiManager
            ->read('services_transcription_projects', $this->getProject()->getId())
            ->getContent()
            ->itemIds();

        foreach (array_chunk($itemIds, 100) as $itemIdsChunk) {
            // Iterate items.
            foreach ($itemIdsChunk as $itemId) {
                $item = $entityManager
                    ->getRepository(Item::class)
                    ->find($itemId);
                // Iterate item media.
                foreach ($item->getMedia() as $media) {
                    $pages = $entityManager
                        ->getRepository(ServicesTranscriptionPage::class)
                        ->findBy(['media' => $media], ['position' => 'asc']);
                    // Iterate media pages, joining the transcriptions.
                    $transcriptionText = [];
                    foreach ($pages as $page) {
                        $transcription = $entityManager
                            ->getRepository(ServicesTranscriptionTranscription::class)
                            ->findOneBy(['page' => $page]);
                        if (in_array($transcription->getJobState(), Mino::COMPLETED_JOB_STATES)) {
                            $transcriptionText[] = $transcription->getText();
                        }
                    }
                    // @todo: Delete all media values with the configured property
                    // @todo: Save media value to the configured property
                }
                // @todo: Delete all item values with the configured property
                // @todo: Save item value to the configured property
            }
            $entityManager->flush();
        }
    }
}
