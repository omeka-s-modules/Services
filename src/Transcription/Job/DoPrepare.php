<?php
namespace Services\Transcription\Job;

use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;

class DoPrepare extends AbstractJob
{
    public function perform()
    {
        /*
        - For each item in project query
            - For each media in item
                - If not already, split the media into images (i.e. pages)
                - For each image
                        - Save image file to Omeka storage
                        - Save image to database
        */
    }
}
