<?php
namespace Services\Transcription\Preprocesser\File;

use Omeka\Entity\Media;

class ImageTiff extends AbstractFilePreprocesser
{
    public function preprocess(Media $media): array
    {
        return $this->multipageSplit($media);
    }
}
