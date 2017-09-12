<?php
namespace Common\Model\Entity;

class Identity extends BaseEntity
{

    protected $id;
    protected $username;
    protected $email;
    protected $createdAt = null;
    protected $updatedAt = null;

    /*
     * setter
     */

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime($createdAt);
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = new \DateTime($updatedAt);
    }

    /*
     * getter
     */

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}