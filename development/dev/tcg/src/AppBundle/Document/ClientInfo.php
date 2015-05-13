<?php
/// src/Acme/StoreBundle/Document/ClientInfo.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


class FrequencyType{
    const  Weekly = "weekly";
    const  TwiceAWeek = "twiceAWeek";
    const  Fortnightly = "fortnightly";
    const  Monthly = "monthly";
    const  WhenNeed = "whenNeed";
}

class PaymentType{
    const  Cash = "cash";
    const  Cheque = "cheque";
    const  Debt = "debt";
}

/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\ClientInfoRepository")
 */
class ClientInfo extends BaseDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $clientId;

    /**
     * @MongoDB\String
     */
    protected $clientName;

    /**
     * @MongoDB\String
     */
    protected $driverLicense;

    /**
     * @MongoDB\String
     */
    protected $tel;

    /**
     * @MongoDB\Date
     */
    protected $birthday;

    /**
     * @MongoDB\String
     */
    protected $address;

    /**
     * @MongoDB\boolean
     */
    protected $isActive;

    /**
     * @MongoDB\Date
     */
    protected $startDate;

    /**
     * @MongoDB\float
     */
    protected $price;

    /** @MongoDB\EmbedMany(targetDocument="PriceHistory") */
    protected $priceHistory = array();

    /** @MongoDB\collection*/
    protected $rotations =  array();

    /**
     * @MongoDB\String
     */
    protected $remark;

    /**
     * @MongoDB\String
     */
    protected $paymentType;

    /**
     * @MongoDB\boolean
     */
    protected $invoiceNeeded;

    /**
     * @MongoDB\String
     */
    protected $invoiceTitle;

    /** @MongoDB\EmbedOne(targetDocument="JobDetail") */
    protected $jobDetail;

    /**
     * @MongoDB\String
     */
    protected $creatorId;

    /**
     * @MongoDB\Date
     */
    protected $createTime;

    /**
     * @MongoDB\Date
     */
    protected $modifyTime;

    public function __construct()
    {
        //$this->priceHistory = new \Doctrine\Common\Collections\ArrayCollection();
    }

    
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
     * Set clientId
     *
     * @param string $clientId
     * @return self
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * Get clientId
     *
     * @return string $clientId
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     * @return self
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
        return $this;
    }

    /**
     * Get clientName
     *
     * @return string $clientName
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set driverLicense
     *
     * @param string $driverLicense
     * @return self
     */
    public function setDriverLicense($driverLicense)
    {
        $this->driverLicense = $driverLicense;
        return $this;
    }

    /**
     * Get driverLicense
     *
     * @return string $driverLicense
     */
    public function getDriverLicense()
    {
        return $this->driverLicense;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return self
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
        return $this;
    }

    /**
     * Get tel
     *
     * @return string $tel
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set birthday
     *
     * @param date $birthday
     * @return self
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * Get birthday
     *
     * @return date $birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * Add priceHistory
     *
     * @param AppBundle\Document\PriceHistory $priceHistory
     */
    public function addPriceHistory(\AppBundle\Document\PriceHistory $priceHistory)
    {
        $this->priceHistory[] = $priceHistory;
    }

    /**
     * Remove priceHistory
     *
     * @param AppBundle\Document\PriceHistory $priceHistory
     */
    public function removePriceHistory(\AppBundle\Document\PriceHistory $priceHistory)
    {
        $this->priceHistory->removeElement($priceHistory);
    }

    /**
     * Get priceHistory
     *
     * @return \Doctrine\Common\Collections\Collection $priceHistory
     */
    public function getPriceHistory()
    {
        return $this->priceHistory;
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

    /**
     * Set paymentType
     *
     * @param string $paymentType
     * @return self
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string $paymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set invoiceNeeded
     *
     * @param boolean $invoiceNeeded
     * @return self
     */
    public function setInvoiceNeeded($invoiceNeeded)
    {
        $this->invoiceNeeded = $invoiceNeeded;
        return $this;
    }

    /**
     * Get invoiceNeeded
     *
     * @return boolean $invoiceNeeded
     */
    public function getInvoiceNeeded()
    {
        return $this->invoiceNeeded;
    }

    /**
     * Set invoiceTitle
     *
     * @param string $invoiceTitle
     * @return self
     */
    public function setInvoiceTitle($invoiceTitle)
    {
        $this->invoiceTitle = $invoiceTitle;
        return $this;
    }

    /**
     * Get invoiceTitle
     *
     * @return string $invoiceTitle
     */
    public function getInvoiceTitle()
    {
        return $this->invoiceTitle;
    }

    /**
     * Set jobDetail
     *
     * @param AppBundle\Document\JobDetail $jobDetail
     * @return self
     */
    public function setJobDetail(\AppBundle\Document\JobDetail $jobDetail)
    {
        $this->jobDetail = $jobDetail;
        return $this;
    }

    /**
     * Get jobDetail
     *
     * @return AppBundle\Document\JobDetail $jobDetail
     */
    public function getJobDetail()
    {
        return $this->jobDetail;
    }

    /**
     * Set creatorId
     *
     * @param string $creatorId
     * @return self
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;
        return $this;
    }

    /**
     * Get creatorId
     *
     * @return string $creatorId
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set createTime
     *
     * @param timestamp $createTime
     * @return self
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
        return $this;
    }

    /**
     * Get createTime
     *
     * @return timestamp $createTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set modifyTime
     *
     * @param timestamp $modifyTime
     * @return self
     */
    public function setModifyTime($modifyTime)
    {
        $this->modifyTime = $modifyTime;
        return $this;
    }

    /**
     * Get modifyTime
     *
     * @return timestamp $modifyTime
     */
    public function getModifyTime()
    {
        return $this->modifyTime;
    }

    public static function clientIdGenerator()
    {
        return "0000-0001";
    }

    public function toString()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($this, 'json');
    }


}
