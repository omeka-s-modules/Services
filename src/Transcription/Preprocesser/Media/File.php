<?php
namespace Services\Transcription\Preprocesser\Media;

use Laminas\Log\Logger;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Omeka\Entity\Media;
use Services\Transcription\Preprocesser\File\Manager;

class File implements MediaPreprocesserInterface
{
    protected $filePreprocesserManager;

    protected $logger;

    public function __construct(Manager $filePreprocesserManager, Logger $logger)
    {
        $this->filePreprocesserManager = $filePreprocesserManager;
        $this->logger = $logger;
    }

    public function preprocess(Media $media): array
    {
        try {
            $filePreprocesser = $this->filePreprocesserManager->get($media->getMediaType());
        } catch (ServiceNotFoundException $e) {
            // File preprocesser not found.
            if ($media->hasThumbnails()) {
                // Fall back on large thumbnail for page.
                return [sprintf('large/%s.jpg', $media->getStorageId())];
            } else {
                $this->logger->notice(sprintf('File preprocesser not available for "%s"', $media->getMediaType()));
                return [];
            }
        }
        $storagePaths = $filePreprocesser->preprocess($media);
        return $storagePaths;
    }
}
