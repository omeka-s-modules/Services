<?php
namespace Services\Transcription\Service\Form;

use Services\Transcription\Form\DoPreprocessForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class DoPreprocessFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new DoPreprocessForm(null, $options);
    }
}
