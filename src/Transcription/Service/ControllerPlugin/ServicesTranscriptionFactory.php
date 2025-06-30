<?php
namespace Services\Transcription\Service\ControllerPlugin;

use Interop\Container\ContainerInterface;
use Services\Transcription\ControllerPlugin\ServicesTranscription;
use Zend\ServiceManager\Factory\FactoryInterface;

class ServicesTranscriptionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ServicesTranscription($services);
    }
}
