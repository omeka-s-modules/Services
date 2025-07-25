<?php
namespace Services\Transcription\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Job;
use Omeka\Entity\User;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class ServicesTranscriptionProject extends AbstractEntity
{
    public function __construct()
    {
    }

    /**
     * @Id
     * @Column(
     *     type="integer",
     *     options={
     *         "unsigned"=true
     *     }
     * )
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $label;

    public function setLabel(string $label) : void
    {
        $this->label = $label;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $modelId;

    public function setModelId(string $modelId) : void
    {
        $this->modelId = $modelId;
    }

    public function getModelId() : string
    {
        return $this->modelId;
    }

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $accessToken;

    public function setAccessToken(string $accessToken) : void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken() : string
    {
        return $this->accessToken;
    }

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $query;

    public function setQuery(?string $query) : void
    {
        $query = trim($query);
        $query = ltrim($query, '?');
        $this->query = $query ?: null;
    }

    public function getQuery() : ?string
    {
        return $this->query;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $owner;

    public function setOwner(?User $owner = null) : void
    {
        $this->owner = $owner;
    }

    public function getOwner() : ?User
    {
        return $this->owner;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Job"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $prepareJob;

    public function setPrepareJob(?Job $prepareJob = null) : void
    {
        $this->prepareJob = $prepareJob;
    }

    public function getPrepareJob() : ?Job
    {
        return $this->prepareJob;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Job"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $transcribeJob;

    public function setTranscribeJob(?Job $transcribeJob = null) : void
    {
        $this->transcribeJob = $transcribeJob;
    }

    public function getTranscribeJob() : ?Job
    {
        return $this->transcribeJob;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Job"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $fetchJob;

    public function setFetchJob(?Job $fetchJob = null) : void
    {
        $this->fetchJob = $fetchJob;
    }

    public function getFetchJob() : ?Job
    {
        return $this->fetchJob;
    }

    /**
     * @Column(
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created;

    public function setCreated(DateTime $created) : void
    {
        $this->created = $created;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $modified;

    public function setModified(?DateTime $modified) : void
    {
        $this->modified = $modified;
    }

    public function getModified() : ?DateTime
    {
        return $this->modified;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs) : void
    {
        $this->setCreated(new DateTime('now'));
    }
}
