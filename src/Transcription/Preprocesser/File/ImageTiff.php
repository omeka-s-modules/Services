<?php
namespace Services\Transcription\Preprocesser\File;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Omeka\Entity\Media;
use Services\Transcription\Preprocesser\File\Manager;

class ImageTiff implements FilePreprocesserInterface
{
    protected $cli;

    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    public function preprocess(Media $media): array
    {
        // @todo: Use ImageMagick to split TIFF, save to storage, return storage IDs
        return [];
    }
}
