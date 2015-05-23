<?php
/// src/Acme/StoreBundle/Document/JobDetailPet.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

class PetKeepingType{
    const DoesNotMatter = "doesNotMatter";
    const KeptInDoor = "keptInDoor";
    const KeptOutDoor ="keptOutDoor";
}

/**
 * @MongoDB\EmbeddedDocument
 */
class JobDetailPet extends BaseDocument
{
    /**
     * @MongoDB\Boolean
     */
    protected $has;

    /**
     * @MongoDB\string
     * */
    protected $notes;

    public function __construct()
    {
        $this->has = false;
        $this->notes = PetKeepingType::DoesNotMatter;
    }

    /**
     * Set has
     *
     * @param boolean $has
     * @return self
     */
    public function setHas($has)
    {
        $this->has = $has;
        return $this;
    }

    /**
     * Get has
     *
     * @return boolean $has
     */
    public function getHas()
    {
        return $this->has;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return self
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Get notes
     *
     * @return string $notes
     */
    public function getNotes()
    {
        return $this->notes;
    }
    /**
     * @var $id
     */
    protected $id;


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }
}
