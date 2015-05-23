<?php
/// src/Acme/StoreBundle/Document/JobDetailKey.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

class KeyKeepingType{
    const KeptByUs = "keptByUs";
    const Original = "original";
    const Kitchen ="kitchen";
}

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
    protected $notes;

    /**
     * @MongoDB\Boolean
     */
    protected $hasAlarm;
    /**
     * @MongoDB\string
     * */
    protected $alarmIn;

    /**
     * @MongoDB\string
     * */
    protected $alarmOut;

    public function __construct()
    {
        $this->has = false;
        $this->notes = KeyKeepingType::KeptByUs;
        $this->hasAlarm = false;
        $this->alarmIn="09:00:AM";
        $this->alarmOut="05:00:PM";
    }
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
     * Set notes
     *
     * @param string $notes
     * @return self
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Get notes
     *
     * @return string $notes
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set hasAlarm
     *
     * @param boolean $hasAlarm
     * @return self
     */
    public function setHasAlarm($hasAlarm)
    {
        $this->hasAlarm = $hasAlarm;
        return $this;
    }

    /**
     * Get hasAlarm
     *
     * @return boolean $hasAlarm
     */
    public function getHasAlarm()
    {
        return $this->hasAlarm;
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
