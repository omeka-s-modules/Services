<?php
namespace Services\Transcription\Preprocesser\File;

use Omeka\Entity\Media;

abstract class AbstractFilePreprocesser implements FilePreprocesserInterface
{
    protected $services;

    public function __construct($services)
    {
        $this->services = $services;
    }

    /**
     * Split a multipage file into its component pages.
     *
     * If images need additional preprocessing (beyond being split), pass a
     * callback function to $preprocessCallback. This will pass 1) the temporary
     * path to the image and 2) the media entity to the callback. The callback
     * should modify the image in place.
     */
    public function multipageSplit(Media $media, ?callable $preprocessCallback = null): array
    {
        $api = $this->services->get('Omeka\ApiManager');
        $tempFileFactory = $this->services->get('Omeka\File\TempFileFactory');
        $cli = $this->services->get('Omeka\Cli');
        $fileStore = $this->services->get('Omeka\File\Store');

        // Copy original file to tmp directory.
        $mediaRepresentation = $api->read('media', $media->getId())->getContent();
        $tempFile = $tempFileFactory->build();
        copy($mediaRepresentation->originalUrl(), $tempFile->getTempPath());

        // Split file using ImageMagick.
        $command = sprintf(
            'convert %s %s',
            escapeshellarg($tempFile->getTempPath()),
            escapeshellarg($tempFile->getTempPath() . '-%05d.jpg')
        );
        $cli->execute($command);

        // Save split files to storage, and delete temp files.
        $command = sprintf('ls -1 %s-*', $tempFile->getTempPath());
        $tempPaths = $cli->execute($command);
        $storagePaths = [];
        foreach (explode("\n", $tempPaths) as $index => $tempPath) {
            if (is_callable($preprocessCallback)) {
                $preprocessCallback($tempPath, $media);
            }
            $storagePath = sprintf('services_transcription_page/%s-%s.jpg', $tempFile->getStorageId(), $index);
            $storagePaths[$index] = $storagePath;
            $fileStore->put($tempPath, $storagePath);
            unlink($tempPath);
        }
        $tempFile->delete();

        return $storagePaths;
    }
}
