<?php
namespace Services\Transcription\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Services\Transcription\Entity\ServicesTranscriptionProject;
use Services\Transcription\Form\ProjectForm;
use Services\Transcription\Form\DoSnapshotForm;
use Services\Transcription\Job\DoSnapshot;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('admin/services/transcription-project');
    }
}
