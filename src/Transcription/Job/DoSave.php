<?php
namespace Services\Transcription\Job;

use Omeka\Entity\Item;
use Omeka\Entity\Value;
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

        $property = $this->getProject()->getProperty();

        $project = $apiManager
            ->read('services_transcription_projects', $this->getProject()->getId())
            ->getContent();

        $itemIds = $project->itemIds();
        $mediaIds = $project->mediaIds();

        // @todo: For all items in this project, delete all values using the configured property
        // @todo: For all media in this project, delete all values using the configured property

        foreach (array_chunk($itemIds, 100) as $itemIdsChunk) {
            // Iterate items.
            foreach ($itemIdsChunk as $itemId) {
                $item = $entityManager
                    ->getRepository(Item::class)
                    ->find($itemId);
                // Iterate item media.
                $itemText = [];
                foreach ($item->getMedia() as $media) {
                    $pages = $entityManager
                        ->getRepository(ServicesTranscriptionPage::class)
                        ->findBy(['media' => $media], ['position' => 'asc']);
                    // Iterate media pages, joining the transcriptions.
                    $mediaText = [];
                    foreach ($pages as $page) {
                        $transcription = $entityManager
                            ->getRepository(ServicesTranscriptionTranscription::class)
                            ->findOneBy(['page' => $page]);
                        if (in_array($transcription->getJobState(), Mino::COMPLETED_JOB_STATES)) {
                            $itemText[] = $transcription->getText();
                            $mediaText[] = $transcription->getText();
                        }
                    }
                    if ($mediaText) {
                        // Save a new media value.
                        $value = new Value;
                        $value->setResource($media);
                        $value->setProperty($property);
                        $value->setType('literal');
                        $value->setValue(implode("\n", $mediaText));
                        $entityManager->persist($value);
                    }
                }
                if ($itemText) {
                    // Save a new item value.
                    $value = new Value;
                    $value->setResource($item);
                    $value->setProperty($property);
                    $value->setType('literal');
                    $value->setValue(implode("\n", $mediaText));
                    $entityManager->persist($value);
                }
            }
            $entityManager->flush();
        }
    }
}
