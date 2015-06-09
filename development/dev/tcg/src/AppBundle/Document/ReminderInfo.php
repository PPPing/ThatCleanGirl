<?php
/// src/Acme/StoreBundle/Document/JobDetail.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 * @MongoDB\EmbeddedDocument
 */
class ReminderInfo extends BaseDocument
{
    /**
     * @MongoDB\Date
     */
    protected $ovenDate;

    /**
     * @MongoDB\Date
     */
    protected $blindsDate;

    /**
     * @MongoDB\Date
     */
    protected $fridgeDate;

    /**
     * @MongoDB\Date
     */
    protected $wardrobesDate;

    /**
     * @MongoDB\Date
     */
    protected $rangeHoodDate;

    /**
     * @MongoDB\Date
     */
    protected $WindowsDate;
    /**
     * @MongoDB\Date
     */
    protected $pantryDate;

    /**
     * @MongoDB\Date
     */
    protected $carpetDate;

    /**
     * @MongoDB\Date
     */
    protected $kitchenDate;

    /**
     * @MongoDB\String
     */
    protected $others;

    public function __construct()
    {

    }

    /**
     * Set ovenDate
     *
     * @param date $ovenDate
     * @return self
     */
    public function setOvenDate($ovenDate)
    {
        $this->ovenDate = $ovenDate;
        return $this;
    }

    /**
     * Get ovenDate
     *
     * @return date $ovenDate
     */
    public function getOvenDate()
    {
        return $this->ovenDate;
    }

    /**
     * Set blindsDate
     *
     * @param date $blindsDate
     * @return self
     */
    public function setBlindsDate($blindsDate)
    {
        $this->blindsDate = $blindsDate;
        return $this;
    }

    /**
     * Get blindsDate
     *
     * @return date $blindsDate
     */
    public function getBlindsDate()
    {
        return $this->blindsDate;
    }

    /**
     * Set fridgeDate
     *
     * @param date $fridgeDate
     * @return self
     */
    public function setFridgeDate($fridgeDate)
    {
        $this->fridgeDate = $fridgeDate;
        return $this;
    }

    /**
     * Get fridgeDate
     *
     * @return date $fridgeDate
     */
    public function getFridgeDate()
    {
        return $this->fridgeDate;
    }

    /**
     * Set wardrobesDate
     *
     * @param date $wardrobesDate
     * @return self
     */
    public function setWardrobesDate($wardrobesDate)
    {
        $this->wardrobesDate = $wardrobesDate;
        return $this;
    }

    /**
     * Get wardrobesDate
     *
     * @return date $wardrobesDate
     */
    public function getWardrobesDate()
    {
        return $this->wardrobesDate;
    }

    /**
     * Set rangeHoodDate
     *
     * @param date $rangeHoodDate
     * @return self
     */
    public function setRangeHoodDate($rangeHoodDate)
    {
        $this->rangeHoodDate = $rangeHoodDate;
        return $this;
    }

    /**
     * Get rangeHoodDate
     *
     * @return date $rangeHoodDate
     */
    public function getRangeHoodDate()
    {
        return $this->rangeHoodDate;
    }

    /**
     * Set windowsDate
     *
     * @param date $windowsDate
     * @return self
     */
    public function setWindowsDate($windowsDate)
    {
        $this->WindowsDate = $windowsDate;
        return $this;
    }

    /**
     * Get windowsDate
     *
     * @return date $windowsDate
     */
    public function getWindowsDate()
    {
        return $this->WindowsDate;
    }

    /**
     * Set pantryDate
     *
     * @param date $pantryDate
     * @return self
     */
    public function setPantryDate($pantryDate)
    {
        $this->pantryDate = $pantryDate;
        return $this;
    }

    /**
     * Get pantryDate
     *
     * @return date $pantryDate
     */
    public function getPantryDate()
    {
        return $this->pantryDate;
    }

    /**
     * Set carpetDate
     *
     * @param date $carpetDate
     * @return self
     */
    public function setCarpetDate($carpetDate)
    {
        $this->carpetDate = $carpetDate;
        return $this;
    }

    /**
     * Get carpetDate
     *
     * @return date $carpetDate
     */
    public function getCarpetDate()
    {
        return $this->carpetDate;
    }

    /**
     * Set kitchenDate
     *
     * @param date $kitchenDate
     * @return self
     */
    public function setKitchenDate($kitchenDate)
    {
        $this->kitchenDate = $kitchenDate;
        return $this;
    }

    /**
     * Get kitchenDate
     *
     * @return date $kitchenDate
     */
    public function getKitchenDate()
    {
        return $this->kitchenDate;
    }
    /**
     * Set others
     *
     * @param string $others
     * @return self
     */
    public function setOthers($others)
    {
        $this->others = $others;
        return $this;
    }

    /**
     * Get others
     *
     * @return string $others
     */
    public function getOthers()
    {
        return $this->others;
    }

    public function loadFromArray(array $info)
    {
        if($info===null){
            return $this;
        }
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (strpos($method, 'set') === 0) {
                $key = lcfirst(substr($method, 3));
                if(isset($info[$key])){
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
        }
        return $this;
    }


}
