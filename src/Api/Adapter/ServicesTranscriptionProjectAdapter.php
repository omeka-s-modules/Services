<?php
namespace Services\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;
use Omeka\Entity\EntityInterface;
use Services\Api\Representation\ServicesTranscriptionProjectRepresentation;
use Services\Entity\ServicesTranscriptionProject;

class ServicesTranscriptionProjectAdapter extends AbstractEntityAdapter
{
    public function getResourceName()
    {
        return 'services_transcription_project';
    }

    public function getRepresentationClass()
    {
        return ServicesTranscriptionProjectRepresentation::class;
    }

    public function getEntityClass()
    {
        return ServicesTranscriptionProject::class;
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
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
