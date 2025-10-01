<?php
namespace Services\Transcription\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PageController extends AbstractActionController
{
    public function showAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('project-id'))->getContent();
        $item = $this->api()->read('items', $this->params('item-id'))->getContent();
        $media = $this->api()->read('media', $this->params('media-id'))->getContent();
        $page = $this->api()->read('services_transcription_pages', $this->params('page-id'))->getContent();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('item', $item);
        $view->setVariable('media', $media);
        $view->setVariable('page', $page);
        return $view;
    }
}
