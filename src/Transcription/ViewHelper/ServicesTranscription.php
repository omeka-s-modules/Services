<?php
namespace Services\Transcription\ViewHelper;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Api\Representation\ItemRepresentation;
use Omeka\Api\Representation\MediaRepresentation;

class ServicesTranscription extends AbstractHelper
{
    protected $services;

    protected $bcRouteMap;

    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
        $this->bcRouteMap = include 'breadcrumbs_route_map.php';
    }

    public function breadcrumbs(): string
    {
        $bc = [];
        $view = $this->getView();
        $routeMatch = $this->services->get('Application')->getMvcEvent()->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();
        if (!isset($this->bcRouteMap[$routeName])) {
            return '';
        }
        foreach ($this->bcRouteMap[$routeName]['breadcrumbs'] as $bcRoute) {
            $params = [];
            foreach ($this->bcRouteMap[$bcRoute]['params'] as $bcParam) {
                $params[$bcParam] = $routeMatch->getParam($bcParam);
            }
            $bc[] = $view->hyperlink($view->translate($this->bcRouteMap[$bcRoute]['text']), $view->url($bcRoute, $params));
        }
        $bc[] = $view->translate($this->bcRouteMap[$routeName]['text']);
        return sprintf('<div class="breadcrumbs">%s</div>', implode('<div class="separator"></div>', $bc));
    }

    public function pageCount(AbstractResourceEntityRepresentation $resource): int
    {
        $apiManager = $this->services->get('Omeka\ApiManager');
        $query = ['limit' => 0];
        if ($resource instanceof ItemRepresentation) {
            $query['services_transcription_item_id'] = $resource->id();
        } elseif ($resource instanceof MediaRepresentation) {
            $query['services_transcription_media_id'] = $resource->id();
        }
        $response = $apiManager->search('services_transcription_pages', $query);
        return $response->getTotalResults();
    }
}
