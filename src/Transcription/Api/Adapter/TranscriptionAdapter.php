<?php
namespace Services\Transcription\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;
use Omeka\Entity\EntityInterface;
use Services\Transcription\Api\Representation\TranscriptionRepresentation;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class TranscriptionAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [];

    public function getResourceName()
    {
        return 'services_transcription_transcriptions';
    }

    public function getRepresentationClass()
    {
        return TranscriptionRepresentation::class;
    }

    public function getEntityClass()
    {
        return ServicesTranscriptionTranscription::class;
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (isset($query['project_id']) && is_numeric($query['project_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.project',
                $qb->createNamedParameter($query['project_id'])
            ));
        }
        if (isset($query['item_id']) && is_numeric($query['item_id'])) {
            $pageAlias = $qb->createAlias();
            $qb->innerJoin(
                'omeka_root.page', $pageAlias
            );
            $qb->andWhere($qb->expr()->eq(
                sprintf('%s.item', $pageAlias),
                $query['item_id']
            ));
        }
        if (isset($query['media_id']) && is_numeric($query['media_id'])) {
            $pageAlias = $qb->createAlias();
            $qb->innerJoin(
                'omeka_root.page', $pageAlias
            );
            $qb->andWhere($qb->expr()->eq(
                sprintf('%s.media', $pageAlias),
                $query['media_id']
            ));
        }
        if (isset($query['page_id']) && is_numeric($query['page_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.page',
                $qb->createNamedParameter($query['page_id'])
            ));
        }
        if (isset($query['status']) && is_string($query['status'])) {
            switch ($query['status']) {
                case 'pending':
                    $jobStates = ['created', 'retry', 'active', 'suspended'];
                    break;
                case 'failed':
                    $jobStates = ['cancelled', 'failed'];
                    break;
                case 'completed':
                    $jobStates = ['completed'];
                    break;
                default:
                    $jobStates = [];
            }
            $qb->andWhere($qb->expr()->in(
                'omeka_root.jobState',
                $qb->createNamedParameter($jobStates)
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
