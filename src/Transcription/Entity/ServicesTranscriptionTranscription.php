<?php
namespace Services\Transcription\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\AbstractEntity;
use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionProject;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"project_id", "page_id"}
 *         )
 *     }
 * )
 */
class ServicesTranscriptionTranscription extends AbstractEntity
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
     * @ManyToOne(
     *     targetEntity="Services\Transcription\Entity\ServicesTranscriptionProject",
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $project;

    public function setProject(ServicesTranscriptionProject $project) : void
    {
        $this->project = $project;
    }

    public function getProject() : ServicesTranscriptionProject
    {
        return $this->project;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Services\Transcription\Entity\ServicesTranscriptionPage",
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $page;

    public function setPage(ServicesTranscriptionPage $page) : void
    {
        $this->page = $page;
    }

    public function getPage() : ServicesTranscriptionPage
    {
        return $this->page;
    }

    /**
     * @Column(
     *     type="smallint",
     *     nullable=true
     * )
     */
    protected $status;

    public function setStatus(?int $status) : void
    {
        $this->status = $status;
    }

    public function getStatus() : ?int
    {
        return $this->status;
    }

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=true
     * )
     */
    protected $jobId;

    public function setJobId(?string $jobId) : void
    {
        $this->jobId = $jobId;
    }

    public function getJobId() : ?string
    {
        return $this->jobId;
    }

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $text;

    public function setText(?string $text) : void
    {
        $this->text = $text;
    }

    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $data;

    public function setData(?string $data) : void
    {
        $this->data = $data;
    }

    public function getData() : ?string
    {
        return $this->data;
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
