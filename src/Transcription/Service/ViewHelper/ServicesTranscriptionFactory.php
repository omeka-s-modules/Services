<?php
namespace Services\Transcription\Service\ViewHelper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Services\Transcription\ViewHelper\ServicesTranscription;

class ServicesTranscriptionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ServicesTranscription($services);
    }
}
