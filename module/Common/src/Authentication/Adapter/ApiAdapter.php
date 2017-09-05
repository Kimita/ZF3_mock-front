<?php
namespace Common\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Common\Utility\ApiClient;

class ApiAdapter implements AdapterInterface
{
    private $apiClient = null;
    private $username = null;
    private $password = null;

    public function __construct(ApiClient $client)
    {
        $this->apiClient = $client;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setUserData(array $data)
    {
        if (array_key_exists('id', $data) && array_key_exists('username', $data)) {
            $this->userData = $data;
        }
        return $this;
    }

    /**
      * {@inheritDoc}
      * @see \Zend\Authentication\Adapter\AdapterInterface::authenticate()
      */
    public function authenticate()
    {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
        );
        $result = $this->apiClient->authenticate($params);

        if ( is_array($result) && array_key_exists('user', $result)
          && !empty($result['user']) )
        {
            $response = new Result(
                Result::SUCCESS,
                $result['user'],
                array('Authentication successful.')
            );
        } else {
            $response = new Result(
                Result::FAILURE,
                null,
                array('Invalid credentials.')
            );
        }

        return $response;
    }

}