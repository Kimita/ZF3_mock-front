<?php
namespace Common\Model\Entity;

use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

class BaseEntity
{
    protected $identifier = 'id';

    protected $id;

    /*
     * setter
     */

    public function setId($id)
    {
        $this->id = (int)$id;
    }


    /*
     * getter
     */

    public function getId()
    {
        return $this->id;
    }


    /*
     * utility methods
     */

    public function getIdentifierValue()
    {
        $namingStrategy = new UnderscoreNamingStrategy();
        $funcName = 'get'.ucfirst($namingStrategy->hydrate($this->identifier));
        return $this->$funcName();
    }

    public function getObjIfMatch($key, $val)
    {
        return ($this->$key == $val) ? $this:null;
    }

    public function getStrForSelectOption()
    {
        $str = $this->identifier.': ' . $this->getIdentifierValue();
        return $str;
    }

}