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
    protected $attention;

    /** @MongoDB\EmbedMany(targetDocument="JobDetailItem") */
    protected $items = array();
    public function __construct()
    {
        $this->frequency = "weekly";
        $this->attention = "";
        $this->key = new JobDetailKey();
        $this->pet = new JobDetailPet();

        $jobItem = new JobDetailItem();
        $jobItem->setName("Formal lounge");
        $jobItem->setAmount(1);
        $jobItem->setRequest("");

        $jobItem1 = new JobDetailItem();
        $jobItem1->setName("Formal dining");
        $jobItem1->setAmount(1);
        $jobItem1->setRequest("");

        $jobItem2 = new JobDetailItem();
        $jobItem2->setName("Family room");
        $jobItem2->setAmount(1);
        $jobItem2->setRequest("");

        $this->addItem($jobItem);
        $this->addItem($jobItem1);
        $this->addItem($jobItem2);
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
     * Set attention
     *
     * @param string $attention
     * @return self
     */
    public function setAttention($attention)
    {
        $this->attention = $attention;
        return $this;
    }

    /**
     * Get attention
     *
     * @return string $attention
     */
    public function getAttention()
    {
        return $this->attention;
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
}
