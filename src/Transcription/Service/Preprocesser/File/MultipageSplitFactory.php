<?php
namespace Services\Transcription\Service\Preprocesser\File;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Services\Transcription\Preprocesser\File\MultipageSplit;

class MultipageSplitFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new MultipageSplit(
            $services->get('Omeka\ApiManager'),
            $services->get('Omeka\File\TempFileFactory'),
            $services->get('Omeka\Cli'),
            $services->get('Omeka\File\Store')
        );
    }
}
