<?php
namespace Services\Transcription\Service\Preprocesser;

use Services\Transcription\Preprocesser\Media\Manager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class MediaPreprocesserManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $config = $services->get('Config');
        return new Manager($services, $config['services_module']['transcription']['media_preprocessers']);
    }
}
