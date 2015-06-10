<?php
/// src/Acme/StoreBundle/Document/ServiceInfo.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


/**
 * @MongoDB\Document
 */
class HolidayInfo extends BaseDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\string
     */
    protected $title;

    /**
     * @MongoDB\string
     */
    protected $start;

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
     * Set title
     *
     * @param int $title
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
     * @return int $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    

    /**
     * Set start
     *
     * @param string $start
     * @return self
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * Get start
     *
     * @return string $start
     */
    public function getStart()
    {
        return $this->start;
    }
}
