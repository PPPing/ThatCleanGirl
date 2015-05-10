<?php
/// src/Acme/StoreBundle/Document/JobDetailPet.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

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
    protected $keeping;


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
     * Set keeping
     *
     * @param string $keeping
     * @return self
     */
    public function setKeeping($keeping)
    {
        $this->keeping = $keeping;
        return $this;
    }

    /**
     * Get keeping
     *
     * @return string $keeping
     */
    public function getKeeping()
    {
        return $this->keeping;
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
