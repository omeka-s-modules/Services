<?php
namespace Services\Transcription\Preprocesser\Media;

use Omeka\Entity\Media;

class File implements MediaPreprocesserInterface
{
    public function preprocess(Media $media): array
    {
        // @todo: Implement FilePreprocesserManagerFactory
        $storageIds = [];
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
                    $storageIds[] = $media->getStorageId();
                }
                break;
        }
        return $storageIds;
    }
}
