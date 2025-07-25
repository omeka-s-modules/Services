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

    public function getFormDoPrepare($project)
    {
        $controller = $this->getController();
        $formDoPrepare = $controller->getForm(Form\DoPrepareForm::class, ['project' => $project]);
        $formDoPrepare->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-prepare'], true));
        return $formDoPrepare;
    }
}
