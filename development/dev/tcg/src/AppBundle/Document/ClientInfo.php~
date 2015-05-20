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
    protected $tel;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\Date
     */
    protected $birthday;

    /**
     * @MongoDB\String
     */
    protected $address;

    /**
     * @MongoDB\String
     */
    protected $district;

    /**
     * @MongoDB\boolean
     */
    protected $isActive;

    /**
     * @MongoDB\Date
     */
    protected $startDate;

    /**
     * @MongoDB\Date
     */
    protected $serviceDate;

    /**
     * @MongoDB\String
     */
    protected $serviceTime;

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

    /** @MongoDB\EmbedOne(targetDocument="ReminderInfo") */
    protected $reminderInfo;

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


    function __construct() {
        $this->clientId = null;
        $this->clientName = null;
        $this->tel="";
        $this->email="";
        $this->birthday=new \DateTime("1980-01-01");
        $this->address = "";
        $this->district="";
        $this->isActive=false;
        $this->startDate = new \DateTime('Now');
        $this->serviceDate = new \DateTime('Now');
        $this->serviceTime = "10:00:AM";
        $this->price="998";

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
        $this->remark="";
        $this->paymentType="cash";
        $this->invoiceNeeded=false;
        $this->invoiceTitle="";
        $this->jobDetail = new JobDetail();
        $this->reminderInfo = new ReminderInfo();
        $this->creatorId = null;
        $this->createTime = new \DateTime("NOW");
        $this->modifyTime = new \DateTime("NOW");
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
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * Set district
     *
     * @param string $district
     * @return self
     */
    public function setDistrict($district)
    {
        $this->district = $district;
        return $this;
    }

    /**
     * Get district
     *
     * @return string $district
     */
    public function getDistrict()
    {
        return $this->district;
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
     * Set serviceDate
     *
     * @param date $serviceDate
     * @return self
     */
    public function setServiceDate($serviceDate)
    {
        $this->serviceDate = $serviceDate;
        return $this;
    }

    /**
     * Get serviceDate
     *
     * @return date $serviceDate
     */
    public function getServiceDate()
    {
        return $this->serviceDate;
    }

    /**
     * Set serviceTime
     *
     * @param string $serviceTime
     * @return self
     */
    public function setServiceTime($serviceTime)
    {
        $this->serviceTime = $serviceTime;
        return $this;
    }

    /**
     * Get serviceTime
     *
     * @return string $serviceTime
     */
    public function getServiceTime()
    {
        return $this->serviceTime;
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
     * Set reminderInfo
     *
     * @param AppBundle\Document\ReminderInfo $reminderInfo
     * @return self
     */
    public function setReminderInfo(\AppBundle\Document\ReminderInfo  $reminderInfo)
    {
        $this->reminderInfo = $reminderInfo;
        return $this;
    }

    /**
     * Get jobDetail
     *
     * @return AppBundle\Document\ReminderInfo $reminderInfo
     */
    public function getReminderInfo()
    {
        return $this->reminderInfo;
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


    public function toString()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($this, 'json');
    }


}
