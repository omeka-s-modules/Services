<?php
namespace Services\Transcription\Preprocesser\Media;

use Omeka\Entity\Media;

interface MediaPreprocesserInterface
{
    /**
     * Preprocess the provided media.
     *
     * @param Media $media
     * @return array An array of Omeka storage IDs
     */
    public function preprocess(Media $media): array;
}
