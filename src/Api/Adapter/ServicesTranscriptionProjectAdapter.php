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
        if (Request::UPDATE === $request->getOperation()) {
            $entity->setModified(new DateTime('now'));
        }
        $this->hydrateOwner($request, $entity);
        if ($this->shouldHydrate($request, 'o:label')) {
            $entity->setLabel($request->getValue('o:label'));
        }
        if ($this->shouldHydrate($request, 'o:query')) {
            $entity->setQuery($request->getValue('o:query'));
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        if (!is_string($entity->getLabel()) || '' === $entity->getLabel()) {
            $errorStore->addError('o:label', 'A transcription project must have a label.'); // @translate
        }
    }
}
