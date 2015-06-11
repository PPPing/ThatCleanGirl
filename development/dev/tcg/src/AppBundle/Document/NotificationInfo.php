<?php
/// src/Acme/StoreBundle/Document/ServiceInfo.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class NotificationType{
    const Birthday = "birthday";
    const Clean = "clean";
}

class NotificationStatus{
    const Unconfirmed = "unconfirmed";
    const Confirmed = "confirmed";
}

/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\NotificationInfoRepository")
 */
class NotificationInfo extends BaseDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\string
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
     * @MongoDB\string
     */
    protected $type;

    /**
     * @MongoDB\string
     */
    protected $status;

    /**
     * @MongoDB\string
     */
    protected $title;

    /**
     * @MongoDB\Date
     */
    protected $date;
	
	/** @MongoDB\collection*/
    protected $items =  array();

    /**
     * @MongoDB\Date
     */
    protected $createTime;

    function __construct() {

        $this->createTime = new \DateTime("NOW");

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
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param string $status
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
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set date
     *
     * @param string $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return string $date
     */
    public function getDate()
    {
        return $this->date;
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
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
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
}
