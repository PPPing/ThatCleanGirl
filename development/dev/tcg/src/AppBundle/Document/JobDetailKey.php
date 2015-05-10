<?php
/// src/Acme/StoreBundle/Document/JobDetailKey.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class JobDetailKey extends BaseDocument
{
    /**
     * @MongoDB\Boolean
     */
    protected $has;

    /**
     * @MongoDB\string
     * */
    protected $keeping;

    /**
     * @MongoDB\string
     * */
    protected $alarmIn;

    /**
     * @MongoDB\string
     * */
    protected $alarmOut;


    /**
     * Set has
     *
     * @param boolean $has
     * @return self
     */
    public function setHas($has)
    {
        $this->has = $has;
        return $this;
    }

    /**
     * Get has
     *
     * @return boolean $has
     */
    public function getHas()
    {
        return $this->has;
    }

    /**
     * Set keeping
     *
     * @param string $keeping
     * @return self
     */
    public function setKeeping($keeping)
    {
        $this->keeping = $keeping;
        return $this;
    }

    /**
     * Get keeping
     *
     * @return string $keeping
     */
    public function getKeeping()
    {
        return $this->keeping;
    }

    /**
     * Set alarmIn
     *
     * @param string $alarmIn
     * @return self
     */
    public function setAlarmIn($alarmIn)
    {
        $this->alarmIn = $alarmIn;
        return $this;
    }

    /**
     * Get alarmIn
     *
     * @return string $alarmIn
     */
    public function getAlarmIn()
    {
        return $this->alarmIn;
    }

    /**
     * Set alarmOut
     *
     * @param string $alarmOut
     * @return self
     */
    public function setAlarmOut($alarmOut)
    {
        $this->alarmOut = $alarmOut;
        return $this;
    }

    /**
     * Get alarmOut
     *
     * @return string $alarmOut
     */
    public function getAlarmOut()
    {
        return $this->alarmOut;
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
}
