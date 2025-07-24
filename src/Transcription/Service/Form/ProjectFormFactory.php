<?php
namespace Services\Transcription\Service\Form;

use Services\Transcription\Form\ProjectForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ProjectFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ProjectForm(null, $options);
    }
}
