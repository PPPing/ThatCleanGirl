<?php
/// src/Acme/StoreBundle/Document/ServiceInfo.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class InvoiceStatus{
    const Pending = 0;
    const Sent =1;
}

/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\InvoiceInfoRepository")
 */
class InvoiceInfo extends BaseDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Int
     */
    protected $status;

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
     * @MongoDB\String
     */
    protected $address;

    /**
     * @MongoDB\String
     */
    protected $suburb;

    /**
     * @MongoDB\Float
     */
    protected $price;

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

    /**
     * @MongoDB\Date
     */
    protected $serviceDate;

    /**
     * @MongoDB\String
     */
    protected $serviceStartTime;

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

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    function __construct() {
        $this->status = InvoiceStatus::Pending;
        $this->clientId = null;
        $this->clientName = null;
        $this->tel=null;
        $this->email=null;
        $this->address = null;
        $this->suburb=null;
        $this->price=0;
        $this->paymentType="cash";
        $this->invoiceNeeded=false;
        $this->invoiceTitle=null;

        $this->serviceDate = new \DateTime('Now');
        $this->serviceStartTime = "10:00";

        $this->creatorId = null;
        $this->createTime = new \DateTime("NOW");
        $this->modifyTime = new \DateTime("NOW");
    }

    public function loadFromArray(array $info)
    {
       if(empty($info['id'])){
            //$this->id ->
        }else{
            $this->id = $info['id'];
        }
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (strpos($method, 'set') === 0) {
                $key = lcfirst(substr($method, 3));
                if(isset($info[$key])) {
                    $value = $info[$key];
                    if ($this->endsWith($key, 'date') === true) {
                        $value = date_create_from_format('Y-m-d\TH:i:sT', $value);
                    } else if ($value === "false") {
                        $value = false;
                    } else if ($value === "true") {
                        $value = true;
                    }
                    $this->$method($value);
                }
            }
        }
        return $this;
    }


    /**
     * Set status
     *
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * Set suburb
     *
     * @param string $suburb
     * @return self
     */
    public function setSuburb($suburb)
    {
        $this->suburb = $suburb;
        return $this;
    }

    /**
     * Get suburb
     *
     * @return string $suburb
     */
    public function getSuburb()
    {
        return $this->suburb;
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
     * Set serviceStartTime
     *
     * @param string $serviceStartTime
     * @return self
     */
    public function setServiceStartTime($serviceStartTime)
    {
        $this->serviceStartTime = $serviceStartTime;
        return $this;
    }

    /**
     * Get serviceStartTime
     *
     * @return string $serviceStartTime
     */
    public function getServiceStartTime()
    {
        return $this->serviceStartTime;
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
     * @param date $createTime
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
     * @return date $createTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set modifyTime
     *
     * @param date $modifyTime
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
     * @return date $modifyTime
     */
    public function getModifyTime()
    {
        return $this->modifyTime;
    }
}
