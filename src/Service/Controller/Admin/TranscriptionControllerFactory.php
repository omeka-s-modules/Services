<?php
namespace Services\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Services\Controller\Admin\TranscriptionController;

class TranscriptionControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new TranscriptionController;
    }
}
