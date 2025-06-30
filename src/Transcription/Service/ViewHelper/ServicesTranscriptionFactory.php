<?php
namespace Services\Service\ViewHelper;

use Interop\Container\ContainerInterface;
use Services\ViewHelper\ServicesTranscription;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ServicesTranscriptionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ServicesTranscription($services);
    }
}
