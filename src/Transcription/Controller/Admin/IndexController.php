<?php
namespace Services\Transcription\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Services\Form as ServicesForm;

class IndexController extends AbstractActionController
{
    public function browseAction()
    {
        $this->setBrowseDefaults('created');
        $query = $this->params()->fromQuery();
        $response = $this->api()->search('services_transcription_project', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $projects = $response->getContent();

        $view = new ViewModel;
        $view->setVariable('projects', $projects);
        return $view;
    }

    public function addAction()
    {
        $form = $this->getForm(ServicesForm\TranscriptionProjectForm::class, ['transcription_project' => null]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->create('services_transcription_project', $formData);
                if ($response) {
                    $project = $response->getContent();
                    $this->messenger()->addSuccess('Transcription project successfully added.'); // @translate
                    return $this->redirect()->toRoute('admin/services/transcription/id', ['id' => $project->id(), 'action' => 'show'], true);
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
        $project = $this->api()->read('services_transcription_project', $this->params('id'))->getContent();
        $form = $this->getForm(ServicesForm\TranscriptionProjectForm::class, ['transcription_project' => $project]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->update('services_transcription_project', $project->id(), $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Transcription project successfully edited.');
                    return $this->redirect()->toRoute('admin/services/transcription/id', ['id' => $project->id(), 'action' => 'show'], true);
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
        $project = $this->api()->read('services_transcription_project', $this->params('id'))->getContent();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('formDoPrepareItems', $this->servicesTranscription()->getFormDoPrepareItems($project));
        return $view;
    }
}
