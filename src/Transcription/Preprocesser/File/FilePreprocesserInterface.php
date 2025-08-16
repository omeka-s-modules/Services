<?php
namespace Services\Transcription\Preprocesser\File;

use Omeka\Entity\Media;

interface FilePreprocesserInterface
{
    /**
     * Preprocess the provided media file.
     *
     * @param Media $media
     * @return array An array of Omeka storage IDs
     */
    public function preprocess(Media $media): array;
}
