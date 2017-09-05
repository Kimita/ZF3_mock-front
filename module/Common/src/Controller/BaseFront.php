<?php
namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Hydrator\ClassMethods;
use Common\Utility\ApiClient;

class BaseFront extends AbstractActionController
{
    /**
     *  @var string セッション名
     */
    protected $sesName = 'mock_front';

    /**
     *  @var int セッションの期限が切れるまでの秒数
     */
    protected $sesExpSec = 86400;

    /**
     * @var \Common\Model\Entity\Identity
     */
    protected $identity = null;

    /**
     * @var \Common\Utility\ApiClient
     */
    protected $apiClient;

    /**
     * @var string サイトTOP のルート名
     */
    protected $routeSiteTop = 'site-top';

    public function __construct($apiClient = null)
    {
        $this->apiClient = $apiClient instanceof ApiClient
                            ? $apiClient : new ApiClient();
    }

    protected function setIdentityToSession($identity)
    {
        $session = new Container($this->sesName);
        $session->setExpirationSeconds($this->sesExpSec);
        $session->identity = $identity;
        $this->identity = $identity;
        return $this;
    }

    protected function clearIdentity()
    {
        $session = new Container($this->sesName);
        $session->identity = null;
        $this->identity = null;
        return $this;
    }

    /**
     * 規定のセッション値の有無をチェックする
     */
    protected function isAuthenticated()
    {
        if($this->identity === null){
            $session = new Container($this->sesName);
            $identity = $session->identity;
            $this->identity = $identity ? $identity : null;
        }
        return $this->identity;
    }

    /**
     * @param array $array
     * @param string|object $class
     * @return object
     */
    protected function hydrate(array $array, $class)
    {
        if (is_string($class)) {
            $class = new $class();
        }
        $hydrator = new ClassMethods();
        return $hydrator->hydrate($array, $class);
    }

}