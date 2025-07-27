<?php
namespace Services\Transcription\ControllerPlugin;

use Services\Transcription\Form;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServicesTranscription extends AbstractPlugin
{
    protected $services;

    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
    }

    public function getEntityManager()
    {
        return $this->services->get('Omeka\EntityManager');
    }

    public function getFormDoPreprocess($project)
    {
        $controller = $this->getController();
        $formDoPreprocess = $controller->getForm(Form\DoPreprocessForm::class, ['project' => $project]);
        $formDoPreprocess->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-preprocess'], true));
        return $formDoPreprocess;
    }
}
