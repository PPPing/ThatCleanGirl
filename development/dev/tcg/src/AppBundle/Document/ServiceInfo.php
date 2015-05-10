<?php
/// src/Acme/StoreBundle/Document/ServiceInfo.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ServiceStatus{
    const Pendding = 0;
    const Processing = 1;
    const Completed =2;
    const Reviewed = 3;
}

/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\ServiceInfoRepository")
 */
class ServiceInfo extends BaseDocument
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
     * @MongoDB\boolean
     */
    protected $isConfirmed;

    /**
     * @MongoDB\String
     */
    protected $clientId;

    /**
     * @MongoDB\String
     */
    protected $clientName;

    /**
     * @MongoDB\Date
     */
    protected $serviceDate;

    /**
     * @MongoDB\String
     */
    protected $address;

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
    protected $teamId;

    /**
     * @MongoDB\String
     */
    protected $feedback;

    /**
     * @MongoDB\String
     */
    protected $creatorId;

    /**
     * @MongoDB\Date
     */
    protected $createTime;


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
     * Set isConfirmed
     *
     * @param boolean $isConfirmed
     * @return self
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;
        return $this;
    }

    /**
     * Get isConfirmed
     *
     * @return boolean $isConfirmed
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
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
     * Set teamId
     *
     * @param string $teamId
     * @return self
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
        return $this;
    }

    /**
     * Get teamId
     *
     * @return string $teamId
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * Set feedback
     *
     * @param string $feedback
     * @return self
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
        return $this;
    }

    /**
     * Get feedback
     *
     * @return string $feedback
     */
    public function getFeedback()
    {
        return $this->feedback;
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

    public function loadFromArray(array $info)
    {

       if(empty($info['id'])){
            throw new InvalidArgumentException("clientInfo : Id");
        }else{
            $this->id = $info['id'];
        }

        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler('C:/xampp/htdocs/github/ThatCleanGirl/development/dev/tcg/app/logs/serviceHistory.log', Logger::DEBUG));

        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (strpos($method, 'set') === 0) {
                $key = lcfirst(substr($method, 3));
                $value = $info[$key];
                if( $this->endsWith($key, 'date')===true ){
                    $value = date_create_from_format('Y-m-d\TH:i:sT', $value);
                }else if($value==="false"){
                    $value=false;
                }else if($value==="true") {
                    $value=true;
                }
                $this->$method($value);
            }
        }
        //$log->addDebug(json_encode($this,JSON_PRETTY_PRINT));
        return $this;

    }

   public  static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}
