<?php
/// src/Acme/StoreBundle/Document/JobDetailKey.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class JobDetailAlarm extends BaseDocument
{
    /**
     * @MongoDB\Boolean
     */
    protected $has;


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
        $this->alarmIn="09:00";
        $this->alarmOut="17:00";
    }
    public function loadFromArray(array $info){
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
