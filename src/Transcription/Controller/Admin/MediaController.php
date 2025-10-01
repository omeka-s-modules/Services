<?php
namespace Services\Transcription\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class MediaController extends AbstractActionController
{
    public function showAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('project-id'))->getContent();
        $item = $this->api()->read('items', $this->params('item-id'))->getContent();
        $media = $this->api()->read('media', $this->params('media-id'))->getContent();

        $this->setBrowseDefaults('position', 'asc');
        $query = $this->params()->fromQuery();
        $query['project_id'] = $project->id();
        $query['media_id'] = $media->id();
        $response = $this->api()->search('services_transcription_pages', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $pages = $response->getContent();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('item', $item);
        $view->setVariable('media', $media);
        $view->setVariable('pages', $pages);
        return $view;
    }
}
