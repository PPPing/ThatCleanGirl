<?php
/**
 * Created by PhpStorm.
 * User: Mr.Clock
 * Date: 2015/4/28
 * Time: 0:20
 */

namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JsonSerializable;
use \DateTime;
use Doctrine\Common\Collections\Collection as BaseCollection;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ArrayObject implements JsonSerializable {
    public function __construct($array) {
        $this->array = $array;

        $methods = get_class_methods($this);

    }

    public function jsonSerialize() {
        return $this->array;
    }
}

class BaseDocument implements JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    public function jsonSerialize()
    {


// create a log channel
        //$log = new Logger('BaseDocument');
       // $log->pushHandler(new StreamHandler('C:/xampp/htdocs/github/ThatCleanGirl/development/dev/tcg/app/logs/your.log', Logger::WARNING));
        $jsonData = [];
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (strpos($method, 'get') === 0) {

                $value = $this->$method();

                $key = lcfirst (substr($method, 3));

                if(is_object($value)) {
                    if($value instanceof JsonSerializable){
                        $value = $value->jsonSerialize();
                    }else if(is_a($value,'DateTime')) {
                        $value->setTimezone(new \DateTimeZone("UTC"));
                        $value = $value->format('Y-m-d\TH:i:sO');
                    }else if($value instanceof BaseCollection) {
                        $arrayObjects = array();
                        foreach ($value as $item) {
                           // $log->addError(get_class($item));
                            if($item instanceof JsonSerializable){
                                $arrayObjects[] = $item->jsonSerialize();
                            }
                        }
                        $value = $arrayObjects;
                    }
                    else
                    {
                        //continue;
                    }
                }
                $jsonData[$key] = $value;
            }
        }
        return $jsonData;
    }


}

