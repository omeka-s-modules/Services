<?php
namespace Services\Transcription\Service\Form;

use Services\Transcription\Form\DoPrepareForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class DoPrepareFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new DoPrepareForm(null, $options);
    }
}
