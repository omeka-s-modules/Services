<?php
namespace Services\Transcription\Preprocesser\File;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Omeka\Entity\Media;
use Services\Transcription\Preprocesser\File\Manager;

class ImageTiff implements FilePreprocesserInterface
{
    protected $api;
    protected $tempFileFactory;
    protected $cli;
    protected $fileStore;

    public function __construct($api, $tempFileFactory, $cli, $fileStore)
    {
        $this->api = $api;
        $this->tempFileFactory = $tempFileFactory;
        $this->cli = $cli;
        $this->fileStore = $fileStore;
    }

    public function preprocess(Media $media): array
    {
        // Copy original file to tmp directory.
        $mediaRepresentation = $this->api->read('media', $media->getId())->getContent();
        $tempFile = $this->tempFileFactory->build();
        copy($mediaRepresentation->originalUrl(), $tempFile->getTempPath());

        // Split file using ImageMagick.
        $command = sprintf(
            'convert %s %s',
            escapeshellarg($tempFile->getTempPath()),
            escapeshellarg($tempFile->getTempPath() . '-%05d.jpg')
        );
        $this->cli->execute($command);

        // Save split files to storage, and delete temp files.
        $command = sprintf('ls -1 %s-*', $tempFile->getTempPath());
        $tempPaths = $this->cli->execute($command);
        $storagePaths = [];
        foreach (explode("\n", $tempPaths) as $index => $tempPath) {
            $storagePath = sprintf('services_transcription_page/%s-%s.jpg', $tempFile->getStorageId(), $index);
            $storagePaths[] = $storagePath;
            $this->fileStore->put($tempPath, $storagePath);
            unlink($tempPath);
        }
        $tempFile->delete();

        return $storagePaths;
    }
}
