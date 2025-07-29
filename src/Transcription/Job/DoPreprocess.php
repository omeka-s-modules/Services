<?php
namespace Services\Transcription\Job;

use Services\Transcription\Entity\ServicesTranscriptionPage;

class DoPreprocess extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');

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
                    switch ($media->getRenderer()) {
                        case 'file':
                            switch ($media->getMediaType()) {
                                case 'image/tiff':
                                    // @todo: split into pages, save to Omeka storage
                                    break;
                                case 'application/pdf':
                                    // @todo: split into pages and save to Omeka storage
                                    break;
                                    // Add cases to implement other multipage files.
                                default:
                                    if ($media->hasThumbnails()) {
                                        $page = new ServicesTranscriptionPage;
                                        $page->setItem($item);
                                        $page->setMedia($media);
                                        $page->setStorageId($media->getStorageId());
                                        $page->setPosition(1);
                                        $entityManager->persist($page);
                                    }
                                    break;
                            }
                            break;
                            // Add cases to implement other renderers.
                        default:
                            break;
                    }
                }
            }
            $entityManager->flush();
        }
    }
}
