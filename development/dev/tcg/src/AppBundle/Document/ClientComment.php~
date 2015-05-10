<?php
/// src/Acme/StoreBundle/Document/ClientComment.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class ClientComment extends BaseDocument
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
     * @MongoDB\string
     */
    protected $authorId;

    /**
     * @MongoDB\string
     */
    protected $authorName;

    /**
     * @MongoDB\String
     */
    protected $authorTitle;

    /**
     * @MongoDB\Date
     */
    protected $createDate;

    /**
     * @MongoDB\String
     */
    protected $content;




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
     * Set authorId
     *
     * @param string $authorId
     * @return self
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * Get authorId
     *
     * @return string $authorId
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set authorName
     *
     * @param string $authorName
     * @return self
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * Get authorName
     *
     * @return string $authorName
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Set authorTitle
     *
     * @param string $authorTitle
     * @return self
     */
    public function setAuthorTitle($authorTitle)
    {
        $this->authorTitle = $authorTitle;
        return $this;
    }

    /**
     * Get authorTitle
     *
     * @return string $authorTitle
     */
    public function getAuthorTitle()
    {
        return $this->authorTitle;
    }

    /**
     * Set createDate
     *
     * @param date $createDate
     * @return self
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * Get createDate
     *
     * @return date $createDate
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }
}
