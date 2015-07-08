<?php
/// src/Acme/StoreBundle/Document/ServiceInfo.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\InvoiceHistoryRepository")
 */
class InvoiceHistory extends BaseDocument
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
     * @MongoDB\String
     */
    protected $address;

    /**
     * @MongoDB\String
     */
    protected $suburb;

    /**
     * @MongoDB\String
     */
    protected $invoiceTitle;

    /**
     * @MongoDB\Date
     */
    protected $invoiceDate;

    /**
     * @MongoDB\int
     */
    protected $invoiceYM;

    /** @MongoDB\collection*/
    protected $items =  array();

    /**
     * @MongoDB\Float
     */
    protected $total;

    /**
     * @MongoDB\Float
     */
    protected $gst;

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
        $this->clientId = null;
        $this->clientName = null;
        $this->tel=null;
        $this->email=null;
        $this->address = null;
        $this->suburb=null;

        $this->invoiceTitle=null;
        $this->invoiceDate = new \DateTime("NOW");

        $this->creatorId = null;
        $this->createTime = new \DateTime("NOW");
        $this->modifyTime = new \DateTime("NOW");
    }

    public function loadFromArray(array $info){

        $methods = get_class_methods($this);
        //$log = new Logger('sendInvoice');
        //$log->pushHandler(new StreamHandler( 'C:/xampp/htdocs/github/ThatCleanGirl/development/dev/tcg/app/logs/' .'Invoice.log', Logger::DEBUG));

        foreach ($methods as $method) {
            if (strpos($method, 'set') === 0) {
                $key = lcfirst(substr($method, 3));
                if(isset($info[$key])) {
                    $value = $info[$key];
                     //$log->addDebug("[KEY]  ".json_encode($key,JSON_PRETTY_PRINT));
                     //$log->addDebug("[VALUE]  ".json_encode($value,JSON_PRETTY_PRINT));
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
     * Set invoiceDate
     *
     * @param date $invoiceDate
     * @return self
     */
    public function setInvoiceDate($invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    /**
     * Get invoiceDate
     *
     * @return date $invoiceDate
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * Set invoiceYM
     *
     * @param int $invoiceYM
     * @return self
     */
    public function setInvoiceYM($invoiceYM)
    {
        $this->invoiceYM = $invoiceYM;
        return $this;
    }

    /**
     * Get invoiceYM
     *
     * @return int $invoiceYM
     */
    public function getInvoiceYM()
    {
        return $this->invoiceYM;
    }

    /**
     * Set items
     *
     * @param collection $items
     * @return self
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return collection $items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Get total
     *
     * @return float $total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set gst
     *
     * @param float $gst
     * @return self
     */
    public function setGst($gst)
    {
        $this->gst = $gst;
        return $this;
    }

    /**
     * Get gst
     *
     * @return float $gst
     */
    public function getGst()
    {
        return $this->gst;
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
