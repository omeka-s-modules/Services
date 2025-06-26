<?php
namespace Service\Service\Form;

use Service\Form\TranscriptionProjectForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class TranscriptionProjectFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new TranscriptionProjectForm(null, $options);
    }
}
