<?php
namespace Services\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Services\Form as ServicesForm;

class TranscriptionController extends AbstractActionController
{
    public function browseAction()
    {
        $view = new ViewModel;
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
                    $transcriptionProject = $response->getContent();
                    $this->messenger()->addSuccess('Transcription project successfully added.'); // @translate
                    return $this->redirect()->toRoute('admin/services/transcription-id', ['transcription-id' => $transcriptionProject->id(), 'action' => 'show'], true);
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('transcriptionProject', null);
        $view->setVariable('form', $form);
        return $view;
    }

    public function editAction()
    {
        $transcriptionProject = $this->api()->read('services_transcription_project', $this->params('id'))->getContent();
        $form = $this->getForm(ServicesForm\TranscriptionProjectForm::class, ['transcription_project' => $transcriptionProject]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->update('services_transcription_project', $transcriptionProject->id(), $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Transcription project successfully edited.');
                    return $this->redirect()->toRoute('admin/services/transcription-id', ['transcription-id' => $transcriptionProject->id(), 'action' => 'show'], true);
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        } else {
            $data = $import->getJsonLd();
            $data['o-module-osii:local_item_set'] = $data['o-module-osii:local_item_set'] ? $data['o-module-osii:local_item_set']->id() : null;
            $form->setData($data);
        }

        $view = new ViewModel;
        $view->setVariable('transcriptionProject', $transcriptionProject);
        $view->setVariable('form', $form);
        return $view;
    }

    public function showAction()
    {
        $transcriptionProject = $this->api()->read('services_transcription_project', $this->params('id'))->getContent();

        $view = new ViewModel;
        $view->setVariable('transcriptionProject', $transcriptionProject);
        return $view;
    }
}
