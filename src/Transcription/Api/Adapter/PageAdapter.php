<?php
namespace Services\Transcription\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;
use Omeka\Entity\EntityInterface;
use Services\Transcription\Api\Representation\PageRepresentation;
use Services\Transcription\Entity\ServicesTranscriptionPage;

class PageAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'position' => 'position',
    ];

    public function getResourceName()
    {
        return 'services_transcription_pages';
    }

    public function getRepresentationClass()
    {
        return PageRepresentation::class;
    }

    public function getEntityClass()
    {
        return ServicesTranscriptionPage::class;
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $apiManager = $this->getServiceLocator()->get('Omeka\ApiManager');

        if (isset($query['project_id']) && is_numeric($query['project_id'])) {
            // Filter by items in project.
            $project = $apiManager
                ->read('services_transcription_projects', $query['project_id'])
                ->getContent();
            $qb->andWhere($qb->expr()->in(
                'omeka_root.item',
                $qb->createNamedParameter($project->itemIds())
            ));
        }
        if (isset($query['item_id']) && is_numeric($query['item_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.item',
                $query['item_id']
            ));
        }
        if (isset($query['media_id']) && is_numeric($query['media_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.media',
                $query['media_id']
            ));
        }
    }

    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
    }
}
