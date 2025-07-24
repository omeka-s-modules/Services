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

    public function getFormDoSnapshot($project)
    {
        $controller = $this->getController();
        $formDoSnapshot = $controller->getForm(Form\DoSnapshotForm::class, ['project' => $project]);
        $formDoSnapshot->setAttribute('action', $controller->url()->fromRoute('admin/transcription/id', ['action' => 'do-snapshot'], true));
        return $formDoSnapshot;
    }
}
