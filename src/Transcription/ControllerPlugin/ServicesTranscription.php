<?php
namespace Services\Transcription\ControllerPlugin;

use Doctrine\ORM\EntityManager;
use Laminas\Form\Form;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Services\Transcription\Api\Representation\ProjectRepresentation;
use Services\Transcription\Form\DoPreprocessForm;
use Services\Transcription\Form\DoSaveForm;
use Services\Transcription\Form\DoTranscribeForm;

class ServicesTranscription extends AbstractPlugin
{
    protected $services;

    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->services->get('Omeka\EntityManager');
    }

    /**
     * Prepare and return the DoPreprocessForm form.
     */
    public function getFormDoPreprocess(ProjectRepresentation $project): Form
    {
        $controller = $this->getController();
        $formDoPreprocess = $controller->getForm(DoPreprocessForm::class, ['project' => $project]);
        $formDoPreprocess->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-preprocess'], true));
        return $formDoPreprocess;
    }

    /**
     * Prepare and return the DoTranscribeForm form.
     */
    public function getFormDoTranscribe(ProjectRepresentation $project): Form
    {
        $controller = $this->getController();
        $formDoTranscribe = $controller->getForm(DoTranscribeForm::class, ['project' => $project]);
        $formDoTranscribe->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-transcribe'], true));
        return $formDoTranscribe;
    }

    /**
     * Prepare and return the DoSaveForm form.
     */
    public function getFormDoSave(ProjectRepresentation $project): Form
    {
        $controller = $this->getController();
        $formDoSave = $controller->getForm(DoSaveForm::class, ['project' => $project]);
        $formDoSave->setAttribute('action', $controller->url()->fromRoute('admin/services/transcription-project-id', ['action' => 'do-save'], true));
        return $formDoSave;
    }
}
