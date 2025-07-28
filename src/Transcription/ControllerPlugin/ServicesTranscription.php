<?php
namespace Services\Transcription\ControllerPlugin;

use Services\Transcription\Form\DoPreprocessForm;
use Services\Transcription\Form\DoTranscribeForm;
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
        $formDoPreprocess = $controller->getForm(DoPreprocessForm::class, ['project' => $project]);
        $formDoPreprocess->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-preprocess'], true));
        return $formDoPreprocess;
    }

    public function getFormDoTranscribe($project)
    {
        $controller = $this->getController();
        $formDoTranscribe = $controller->getForm(DoTranscribeForm::class, ['project' => $project]);
        $formDoTranscribe->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-transcribe'], true));
        return $formDoTranscribe;
    }
}
