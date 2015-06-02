<?php
/// src/Acme/StoreBundle/Document/JobDetail.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
 * @MongoDB\EmbeddedDocument
 */
class JobDetail extends BaseDocument
{
    /**
     * @MongoDB\String
     */
    protected $frequency;

    /** @MongoDB\EmbedOne(targetDocument="JobDetailKey") */
    protected $key;

    /** @MongoDB\EmbedOne(targetDocument="JobDetailPet") */
    protected $pet;

    /**
     * @MongoDB\String
     */
    protected $important;

    /** @MongoDB\collection*/
    protected $rotations =  array();

    /** @MongoDB\EmbedMany(targetDocument="JobDetailItem") */
    protected $items = array();
    public function __construct()
    {
        $this->frequency = "weekly";
        $this->important = "";
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

        $this->key = new JobDetailKey();
        $this->pet = new JobDetailPet();

        $jobItem = new JobDetailItem();
        $jobItem->setName("Formal lounge");
        $jobItem->setAmount(1);
        $jobItem->setRequest("");


        $jobItem1 = new JobDetailItem();
        $jobItem1->setName("Family room");
        $jobItem1->setAmount(1);
        $jobItem1->setRequest("");

        $this->addItem($jobItem);
        $this->addItem($jobItem1);
    }

    public function loadFromArray(array $info){

        $methods = get_class_methods($this);
        $log = new Logger('Service');
        $log->pushHandler(new StreamHandler( 'C:/xampp/htdocs/github/ThatCleanGirl/development/dev/tcg/app/logs/' .'Service.log', Logger::DEBUG));

        foreach ($methods as $method) {
            if (strpos($method, 'set') === 0) {
                $key = lcfirst(substr($method, 3));
                if(isset($info[$key])) {
                    $value = $info[$key];
                    $log->addDebug("[KEY]  ".json_encode($key,JSON_PRETTY_PRINT));
                    //$log->addDebug("[VALUE]  ".json_encode($value,JSON_PRETTY_PRINT));
                    if ($this->endsWith($key, 'date') === true) {
                        $value = date_create_from_format('Y-m-d\TH:i:sT', $value);
                    } else if ($value === "false") {
                        $value = false;
                    } else if ($value === "true") {
                        $value = true;
                    } else if($key == 'key'){
                        //continue;
                        $jobDetailKey = new JobDetailKey();
                        $jobDetailKey->loadFromArray($value);
                        $value=$jobDetailKey;
                    } else if($key == 'pet'){
                        //continue;
                        $jobDetailPet = new JobDetailPet();
                        $jobDetailPet->loadFromArray($value);
                        $value=$jobDetailPet;
                    } else if($key =='items'){
                        $items=$value;
                        $newItems = array();
                        foreach($items as $item){
                            $newItem = new JobDetailItem();
                            $newItem->loadFromArray($item);
                            $log->addDebug("[itemA]  ".json_encode($item,JSON_PRETTY_PRINT));
                            //$newItem->setId($item['id']);
                            //$this->addItem($newItem);
                            $newItems[] = $newItem;
                            $log->addDebug("[itemB]  ".json_encode($newItem,JSON_PRETTY_PRINT));
                        }
                        $value = $newItems;
                    }
                    $this->$method($value);
                }
            }
        }
    }
    
    /**
     * Set frequency
     *
     * @param string $frequency
     * @return self
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * Get frequency
     *
     * @return string $frequency
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set key
     *
     * @param AppBundle\Document\JobDetailKey $key
     * @return self
     */
    public function setKey(\AppBundle\Document\JobDetailKey $key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return AppBundle\Document\JobDetailKey $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set pet
     *
     * @param AppBundle\Document\JobDetailPet $pet
     * @return self
     */
    public function setPet(\AppBundle\Document\JobDetailPet $pet)
    {
        $this->pet = $pet;
        return $this;
    }

    /**
     * Get pet
     *
     * @return AppBundle\Document\JobDetailPet $pet
     */
    public function getPet()
    {
        return $this->pet;
    }

    /**
     * Set important
     *
     * @param string $important
     * @return self
     */
    public function setImportant($important)
    {
        $this->important = $important;
        return $this;
    }

    /**
     * Get important
     *
     * @return string $important
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * Add item
     *
     * @param AppBundle\Document\JobDetailItem $item
     */
    public function addItem(\AppBundle\Document\JobDetailItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * Remove item
     *
     * @param AppBundle\Document\JobDetailItem $item
     */
    public function removeItem(\AppBundle\Document\JobDetailItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection $items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items
     *
     * @return \Doctrine\Common\Collections\Collection $items
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
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
}
