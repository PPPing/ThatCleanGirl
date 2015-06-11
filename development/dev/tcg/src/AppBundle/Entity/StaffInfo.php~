<?php
// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="staff_info")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\StaffInfoRepository")
 */
class StaffInfo implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * Use Email as account
     */
    private $account;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roles;

    private $rolesArray;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $staffId;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $personalId;

    /**
     * @ORM\Column(type="string")
     */
    private $photo;

    /**
     * @ORM\Column(type="integer")
     *
     * 0-male 1-female 2-middle
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $tel;

    /**
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $mergencyContactName;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $mergencyContactTel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mergencyContactAddress;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $department;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $team;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $title;


    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    public function getUsername()
    {
        return $this->account;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        if(null===$this->rolesArray) {
            $this->rolesArray = explode(',',$this->roles);
        }
        if(empty($this->rolesArray)) {
            $this->rolesArray[] = 'ROLE_GUEST ';
        }
        return $this->rolesArray;
        //return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {

        return serialize(array(
            $this->id,
            $this->account,
            $this->password
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->account,
            $this->password
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set account
     *
     * @param string $account
     * @return StaffInfo
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return string 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return StaffInfo
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return StaffInfo
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return StaffInfo
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set staffId
     *
     * @param string $staffId
     * @return StaffInfo
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;

        return $this;
    }

    /**
     * Get staffId
     *
     * @return string 
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return StaffInfo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set personalId
     *
     * @param string $personalId
     * @return StaffInfo
     */
    public function setPersonalId($personalId)
    {
        $this->personalId = $personalId;

        return $this;
    }

    /**
     * Get personalId
     *
     * @return string 
     */
    public function getPersonalId()
    {
        return $this->personalId;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return StaffInfo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     * @return StaffInfo
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return StaffInfo
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return StaffInfo
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return StaffInfo
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set mergencyContactName
     *
     * @param string $mergencyContactName
     * @return StaffInfo
     */
    public function setMergencyContactName($mergencyContactName)
    {
        $this->mergencyContactName = $mergencyContactName;

        return $this;
    }

    /**
     * Get mergencyContactName
     *
     * @return string 
     */
    public function getMergencyContactName()
    {
        return $this->mergencyContactName;
    }

    /**
     * Set mergencyContactTel
     *
     * @param string $mergencyContactTel
     * @return StaffInfo
     */
    public function setMergencyContactTel($mergencyContactTel)
    {
        $this->mergencyContactTel = $mergencyContactTel;

        return $this;
    }

    /**
     * Get mergencyContactTel
     *
     * @return string 
     */
    public function getMergencyContactTel()
    {
        return $this->mergencyContactTel;
    }

    /**
     * Set mergencyContactAddress
     *
     * @param string $mergencyContactAddress
     * @return StaffInfo
     */
    public function setMergencyContactAddress($mergencyContactAddress)
    {
        $this->mergencyContactAddress = $mergencyContactAddress;

        return $this;
    }

    /**
     * Get mergencyContactAddress
     *
     * @return string 
     */
    public function getMergencyContactAddress()
    {
        return $this->mergencyContactAddress;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return StaffInfo
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set team
     *
     * @param string $team
     * @return StaffInfo
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return string 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return StaffInfo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
