<?php
namespace Services\Transcription\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ItemController extends AbstractActionController
{
    public function showAction()
    {
        $project = $this->api()->read('services_transcription_projects', $this->params('project-id'))->getContent();
        $item = $this->api()->read('items', $this->params('item-id'))->getContent();

        $this->setBrowseDefaults('created');
        $query = $this->params()->fromQuery();
        $query['item_id'] = $item->id();
        $response = $this->api()->search('media', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $medias = $response->getContent();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('item', $item);
        $view->setVariable('medias', $medias);
        return $view;
    }
}
