<?php
namespace Services\Transcription\Service\Preprocesser\File;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Services\Transcription\Preprocesser\File\ImageTiff;

class ImageTiffFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ImageTiff(
            $services->get('Omeka\Cli')
        );
    }
}
