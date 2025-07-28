<?php
namespace Services\Transcription\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Services\Transcription\Entity\ServicesTranscriptionProject;
use Services\Transcription\Form\ProjectForm;
use Services\Transcription\Form\DoPreprocessForm;
use Services\Transcription\Form\DoTranscribeForm;
use Services\Transcription\Job\DoPreprocess;
use Services\Transcription\Job\DoTranscribe;

class ProjectController extends AbstractActionController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function browseAction()
    {
        $this->setBrowseDefaults('created');
        $query = $this->params()->fromQuery();
        $response = $this->api()->search('services_transcription_projects', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $projects = $response->getContent();

        $view = new ViewModel;
        $view->setVariable('projects', $projects);
        return $view;
    }

    public function addAction()
    {
        $form = $this->getForm(ProjectForm::class, ['project' => null]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->create('services_transcription_projects', $formData);
                if ($response) {
                    $project = $response->getContent();
                    $this->messenger()->addSuccess('Transcription project successfully added.'); // @translate
                    return $this->redirect()->toRoute('admin/services/transcription-project-id', ['id' => $project->id(), 'action' => 'show'], true);
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('project', null);
        $view->setVariable('form', $form);
        return $view;
    }

    public function editAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('id'))->getContent();
        $form = $this->getForm(ProjectForm::class, ['project' => $project]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->update('services_transcription_projects', $project->id(), $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Transcription project successfully edited.');
                    return $this->redirect()->toRoute('admin/services/transcription-project-id', ['id' => $project->id(), 'action' => 'show'], true);
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        } else {
            $data = $project->getJsonLd();
            $form->setData($data);
        }

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('form', $form);
        return $view;
    }

    public function showAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('id'))->getContent();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('formDoPreprocess', $this->servicesTranscription()->getFormDoPreprocess($project));
        $view->setVariable('formDoTranscribe', $this->servicesTranscription()->getFormDoTranscribe($project));
        return $view;
    }

    public function doPreprocessAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('id'))->getContent();
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(DoPreprocessForm::class, ['project' => $project]);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $job = $this->jobDispatcher()->dispatch(DoPreprocess::class, ['project_id' => $project->id()]);
                $entity = $this->entityManager->find(ServicesTranscriptionProject::class, $project->id());
                $entity->setPreprocessJob($job);
                $this->entityManager->flush();
                $this->messenger()->addSuccess('Preparing items for transcription. This may take a while.'); // @translate
            }
        }
        return $this->redirect()->toRoute(null, ['action' => 'show'], true);
    }

    public function doTranscribeAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('id'))->getContent();
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(DoTranscribeForm::class, ['project' => $project]);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $job = $this->jobDispatcher()->dispatch(DoTranscribe::class, ['project_id' => $project->id()]);
                $entity = $this->entityManager->find(ServicesTranscriptionProject::class, $project->id());
                $entity->setTranscribeJob($job);
                $this->entityManager->flush();
                $this->messenger()->addSuccess('Transcribing pages. This may take a while.'); // @translate
            }
        }
        return $this->redirect()->toRoute(null, ['action' => 'show'], true);
    }
}
