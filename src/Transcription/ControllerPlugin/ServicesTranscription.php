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

    public function getFormDoPrepareItems($project)
    {
        $controller = $this->getController();
        $formDoPrepareItems = $controller->getForm(Form\TranscriptionProjectDoPrepareItemsForm::class, ['import' => $project]);
        $formDoPrepareItems->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription', ['action' => 'do-prepare-items'], true));
        return $formDoPrepareItems;
    }
}
