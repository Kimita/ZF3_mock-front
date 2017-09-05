<?php
namespace Common;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Authentication\AuthenticationService;
use Common\Model\IndexManager;
use Common\Form\LoginForm;
use Common\Authentication\Adapter\ApiAdapter;
use Common\Utility\ApiClient;

class Module
{
    /** @var \Zend\Mvc\MvcEvent */
    protected $event = null;

    public function onBootstrap(MvcEvent $e)
    {
        $this->event = $e;
    }

    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                ApiClient::class                => InvokableFactory::class,
                ApiAdapter::class               => function ($sm) { return new ApiAdapter($sm->get(ApiClient::class)); },
                AuthenticationService::class    => InvokableFactory::class,
                IndexManager::class             => function ($sm) {
                    $manager = new IndexManager($sm);
                    $routeName = $this->event->getRouteMatch()->getMatchedRouteName();
                    switch (true) {
                        case $routeName === 'site-login':
                            $manager->setForm(new LoginForm())
                                    ->setAuthAdapter($sm->get(ApiAdapter::class), $sm->get(AuthenticationService::class));
                            break;
                    }
                    return $manager;
                },
            ]
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($sm) {
                    $ctrlObj = new Controller\IndexController();
                    return $ctrlObj->setManager($sm->get(IndexManager::class));
                }
            ]
        ];
    }
}