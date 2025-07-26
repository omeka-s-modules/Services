<?php
namespace Services\Transcription\Job;

use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;
use Services\Transcription\Entity\ServicesTranscriptionImage;

class DoPrepare extends AbstractJob
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

        // Get the item IDs.
        parse_str($project->getQuery(), $query);
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
                    $transcriptionImages = $entityManager
                        ->getRepository('Services\Transcription\Entity\ServicesTranscriptionImage')
                        ->findBy(['media' => $media]);
                    if ($transcriptionImages) {
                        // Transcription images already created.
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
                                        $transcriptionImage = new ServicesTranscriptionImage;
                                        $transcriptionImage->setItem($item);
                                        $transcriptionImage->setMedia($media);
                                        $transcriptionImage->setStorageId($media->getStorageId());
                                        $transcriptionImage->setPosition(1);
                                        $entityManager->persist($transcriptionImage);
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
