<?php
namespace Services\Transcription\ViewHelper;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;
use Omeka\Api\Representation\RepresentationInterface;
use Omeka\Api\Representation\ItemRepresentation;
use Omeka\Api\Representation\MediaRepresentation;
use Services\Transcription\Api\Representation\ProjectRepresentation;
use Services\Transcription\Api\Representation\PageRepresentation;

class ServicesTranscription extends AbstractHelper
{
    protected $services;

    protected $bcRouteMap;

    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
        $this->bcRouteMap = include 'breadcrumbs_route_map.php';
    }

    /**
     * Get breadcrumbs markup.
     */
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

    /**
     * Get the page count for an item or media.
     */
    public function pageCount(RepresentationInterface $resource): int
    {
        $apiManager = $this->services->get('Omeka\ApiManager');
        $query = ['limit' => 0];
        if ($resource instanceof ItemRepresentation) {
            $query['item_id'] = $resource->id();
        } elseif ($resource instanceof MediaRepresentation) {
            $query['media_id'] = $resource->id();
        }
        $response = $apiManager->search('services_transcription_pages', $query);
        return $response->getTotalResults();
    }

    /**
     * Get the transcription count for an item, media, or page.
     */
    public function transcriptionCount(
        RepresentationInterface $resource,
        ProjectRepresentation $project,
        ?string $status = null
    ): int {
        $apiManager = $this->services->get('Omeka\ApiManager');
        $query = [
            'limit' => 0,
            'project_id' => $project->id(),
            'status' => $status,
        ];
        if ($resource instanceof ItemRepresentation) {
            $query['item_id'] = $resource->id();
        } elseif ($resource instanceof MediaRepresentation) {
            $query['media_id'] = $resource->id();
        } elseif ($resource instanceof PageRepresentation) {
            $query['page_id'] = $resource->id();
        }
        $response = $apiManager->search('services_transcription_transcriptions', $query);
        return $response->getTotalResults();
    }
}
