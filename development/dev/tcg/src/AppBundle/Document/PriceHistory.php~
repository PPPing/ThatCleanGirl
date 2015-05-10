<?php
/// src/Acme/StoreBundle/Document/PriceHistory.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class PriceHistory extends BaseDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Float
     */
    protected $price;

    /**
     * @MongoDB\Date
     */
    protected $startDate;

    /**
     * @MongoDB\String
     */
    protected $remark;


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
     * Set price
     *
     * @param float $price
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set startDate
     *
     * @param date $startDate
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return date $startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set remark
     *
     * @param string $remark
     * @return self
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
        return $this;
    }

    /**
     * Get remark
     *
     * @return string $remark
     */
    public function getRemark()
    {
        return $this->remark;
    }
}
