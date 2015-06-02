<?php
/// src/Acme/StoreBundle/Document/JobDetailItem.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class JobDetailItem extends BaseDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\string
     */
    public $name;

    /**
     * @MongoDB\int
     * */
    public $amount;

    /**
     * @MongoDB\string
     * */
    public $request;

    public function loadFromArray(array $info){
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (strpos($method, 'set') === 0) {
                $key = lcfirst(substr($method, 3));
                if(isset($info[$key])) {
                    $value = $info[$key];
                    $this->$method($value);
                }
            }
        }
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
     * Set id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set amount
     *
     * @param int $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return int $amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set request
     *
     * @param string $request
     * @return self
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request
     *
     * @return string $request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
