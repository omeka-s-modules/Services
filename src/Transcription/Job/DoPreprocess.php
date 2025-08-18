<?php
namespace Services\Transcription\Job;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Services\Transcription\Entity\ServicesTranscriptionPage;

class DoPreprocess extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');
        $mediaPreprocesserManager = $this->get('Services\Transcription\MediaPreprocesserManager');

        // Get the item IDs.
        parse_str($this->getProject()->getQuery(), $query);
        $query['has_media'] = true;
        $itemIds = $apiManager->search('items', $query, ['returnScalar' => 'id'])->getContent();

        foreach (array_chunk($itemIds, 100) as $itemIdsChunk) {
            // Iterate items.
            foreach ($itemIdsChunk as $itemId) {
                $item = $entityManager
                    ->getRepository('Omeka\Entity\Item')
                    ->find($itemId);
                // Iterate item media.
                foreach ($item->getMedia() as $media) {
                    $pages = $entityManager
                        ->getRepository('Services\Transcription\Entity\ServicesTranscriptionPage')
                        ->findBy(['media' => $media]);
                    if ($pages) {
                        // Pages already created.
                        continue;
                    }
                    try {
                        $mediaPreprocesser = $mediaPreprocesserManager->get($media->getRenderer());
                    } catch (ServiceNotFoundException $e) {
                        // Media preprocesser not found.
                        continue;
                    }
                    $storagePaths = $mediaPreprocesser->preprocess($media);
                    foreach ($storagePaths as $storagePath) {
                        $page = new ServicesTranscriptionPage;
                        $page->setItem($item);
                        $page->setMedia($media);
                        $page->setStoragePath($storagePath);
                        $page->setPosition(1);
                        $entityManager->persist($page);
                    }
                }
            }
            $entityManager->flush();
        }
    }
}
