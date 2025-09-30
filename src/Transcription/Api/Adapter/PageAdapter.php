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
    protected $sortFields = [];

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
        if (isset($query['project_id']) && is_numeric($query['project_id'])) {
            $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
            $apiManager = $this->getServiceLocator()->get('Omeka\ApiManager');

            // Get the project.
            $project = $entityManager
                ->getRepository('Services\Transcription\Entity\ServicesTranscriptionProject')
                ->find($query['project_id']);

            // Get the item IDs.
            parse_str($project->getQuery(), $query);
            $itemIds = $apiManager->search('items', $query, ['returnScalar' => 'id'])->getContent();

            // Filter by items in project.
            $qb->andWhere($qb->expr()->in(
                'omeka_root.item',
                $qb->createNamedParameter($itemIds)
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
