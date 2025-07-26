<?php
namespace Services\Transcription\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Item;
use Omeka\Entity\Media;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class ServicesTranscriptionImage extends AbstractEntity
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
     *     targetEntity="Omeka\Entity\Item",
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $item;

    public function setItem(Item $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : Item
    {
        return $this->item;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Media",
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $media;

    public function setMedia(Media $media) : void
    {
        $this->media = $media;
    }

    public function getMedia() : Media
    {
        return $this->media;
    }

    /**
     * @Column(
     *     type="string",
     *     length=190,
     *     nullable=true
     * )
     */
    protected $storageId;

    public function setStorageId(string $storageId) : void
    {
        $this->storageId = $storageId;
    }

    public function getStorageId() : string
    {
        return $this->storageId;
    }

    /**
     * @Column(
     *     type="integer",
     *     nullable=true
     * )
     */
    protected $position;

    public function setPosition(int $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : int
    {
        return $this->position;
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
