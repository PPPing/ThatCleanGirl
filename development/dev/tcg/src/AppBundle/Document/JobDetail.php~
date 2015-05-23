<?php
/// src/Acme/StoreBundle/Document/JobDetail.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 * @MongoDB\EmbeddedDocument
 */
class JobDetail extends BaseDocument
{
    /**
     * @MongoDB\String
     */
    protected $frequency;

    /** @MongoDB\EmbedOne(targetDocument="JobDetailKey") */
    protected $key;

    /** @MongoDB\EmbedOne(targetDocument="JobDetailPet") */
    protected $pet;

    /**
     * @MongoDB\String
     */
    protected $important;

    /** @MongoDB\collection*/
    protected $rotations =  array();

    /** @MongoDB\EmbedMany(targetDocument="JobDetailItem") */
    protected $items = array();
    public function __construct()
    {
        $this->frequency = "weekly";
        $this->important = "";
        $r1=new \stdClass();
        $r1->key = "week 1";
        $r1->value = "";
        $r2=new \stdClass();
        $r2->key = "week 2";
        $r2->value = "";
        $r3=new \stdClass();
        $r3->key = "week 3";
        $r3->value = "";
        $r4=new \stdClass();
        $r4->key = "week 4";
        $r4->value = "";
        $this->rotations = array($r1,$r2,$r3,$r4);

        $this->key = new JobDetailKey();
        $this->pet = new JobDetailPet();

        $jobItem = new JobDetailItem();
        $jobItem->setName("Formal lounge");
        $jobItem->setAmount(1);
        $jobItem->setRequest("");


        $jobItem1 = new JobDetailItem();
        $jobItem1->setName("Family room");
        $jobItem1->setAmount(1);
        $jobItem1->setRequest("");

        $this->addItem($jobItem);
        $this->addItem($jobItem1);
    }
    
    /**
     * Set frequency
     *
     * @param string $frequency
     * @return self
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * Get frequency
     *
     * @return string $frequency
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set key
     *
     * @param AppBundle\Document\JobDetailKey $key
     * @return self
     */
    public function setKey(\AppBundle\Document\JobDetailKey $key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return AppBundle\Document\JobDetailKey $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set pet
     *
     * @param AppBundle\Document\JobDetailPet $pet
     * @return self
     */
    public function setPet(\AppBundle\Document\JobDetailPet $pet)
    {
        $this->pet = $pet;
        return $this;
    }

    /**
     * Get pet
     *
     * @return AppBundle\Document\JobDetailPet $pet
     */
    public function getPet()
    {
        return $this->pet;
    }

    /**
     * Set important
     *
     * @param string $important
     * @return self
     */
    public function setImportant($important)
    {
        $this->important = $important;
        return $this;
    }

    /**
     * Get important
     *
     * @return string $important
     */
    public function getImportant()
    {
        return $this->important;
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


    /**
     * Add item
     *
     * @param AppBundle\Document\JobDetailItem $item
     */
    public function addItem(\AppBundle\Document\JobDetailItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * Remove item
     *
     * @param AppBundle\Document\JobDetailItem $item
     */
    public function removeItem(\AppBundle\Document\JobDetailItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection $items
     */
    public function getItems()
    {
        return $this->items;
    }


    /**
     * Set rotations
     *
     * @param hash $rotations
     * @return self
     */
    public function setRotations($rotations)
    {
        $this->rotations = $rotations;
        return $this;
    }

    /**
     * Get rotations
     *
     * @return hash $rotations
     */
    public function getRotations()
    {
        return $this->rotations;
    }
}
