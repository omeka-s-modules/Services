<?php
namespace Services\Transcription\Preprocesser\Media;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Omeka\Entity\Media;
use Services\Transcription\Preprocesser\File\Manager;

class File implements MediaPreprocesserInterface
{
    protected $filePreprocesserManager;

    public function __construct(Manager $filePreprocesserManager)
    {
        $this->filePreprocesserManager = $filePreprocesserManager;
    }

    public function preprocess(Media $media): array
    {
        // @todo: Implement file preprocessers for "application/pdf"
        try {
            $filePreprocesser = $this->filePreprocesserManager->get($media->getMediaType());
        } catch (ServiceNotFoundException $e) {
            // File preprocesser not found.
            return $media->hasThumbnails() ? [sprintf('large/%s.jpg', $media->getStorageId())] : [];
        }
        $storagePaths = $filePreprocesser->preprocess($media);
        return $storagePaths;
    }
}
