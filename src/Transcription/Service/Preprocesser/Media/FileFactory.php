<?php
namespace Services\Transcription\Service\Preprocesser\Media;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Services\Transcription\Preprocesser\Media\File;

class FileFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $filePreprocesserManager = $services->get('Services\Transcription\FilePreprocesserManager');
        return new File($filePreprocesserManager);
    }
}
