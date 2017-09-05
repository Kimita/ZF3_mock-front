<?php
namespace Common\Controller;

use Zend\View\Model\ViewModel;
use Common\Controller\BaseFront;
use Common\Model\Entity\Identity;

class IndexController extends BaseFront
{

    /** @var \Common\Model\IndexManager */
    protected $manager;

    /**
     * @param \Common\Model\BaseManager     $manager
     */
    public function setManager($manager = null)
    {
        $this->manager = $manager;
        return $this;
    }

    public function indexAction()
    {
        $this->layout()->setVariable('navPosition', $this->routeSiteTop);
        return new ViewModel();
    }

    public function loginAction()
    {
        $flashMessenger = $this->flashMessenger();
        if ($this->isAuthenticated()) {
            $flashMessenger->addInfoMessage('ログイン済みのためリダイレクトしました');
            return $this->redirect()->toRoute($this->routeSiteTop, ['action'=>'contents']);
        }

        $mgr = $this->manager;

        $this->layout()->setVariable('navPosition', 'site-login');
        $viewData = array('status' => [
            'result'=>false,
            'message'=>array(),
            'user' => [],
        ]);

        $viewData['loginForm'] = $this->makeForm($this->url()->fromRoute($this->routeSiteTop));

        $identity = [];
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost()->toArray();
            $mgr->setPostData($postData);
            $res = $mgr->checkPostData();
            if ($res['result']) {
                $authResult = $mgr->authenticate();
                if (array_key_exists('user', $authResult) && $authResult['user']) {
                    $identity = $this->hydrate($authResult['user'], new Identity());
                    $this->setIdentityToSession($identity);
                    $this->layout()->setVariable('identity', true);

                    $flashMessenger->addInfoMessage('ログインに成功したためリダイレクトしました');
                    return $this->redirect()->toRoute($this->routeSiteTop, ['action'=>'contents']);
                } else {
                    _appError(__METHOD__.' ここを通ることがあってはならない');
                    $viewData['status'] = $res;
                }
            } else {
                $viewData['status'] = $res;
            }
        } else {
            /*
             * 仮処理
             * TODO:本来ならちゃんとしたtokenを解析して扱うようにしたいところ
             */
            $id = $this->getEvent()->getRouteMatch()->getParam('id');
            if (ctype_digit($id)) {
                $res = $this->apiClient->getUser($id);
                if (array_key_exists('user', $res) && $res['user']) {
                    $identity = $this->hydrate($res['user'], new Identity());
                    $this->setIdentityToSession($identity);
                    $this->layout()->setVariable('identity', true);

                    $flashMessenger->addInfoMessage('ログインに成功したためリダイレクトしました');
                    return $this->redirect()->toRoute($this->routeSiteTop, ['action'=>'contents']);
                }
            }
        }

        return $viewData;
    }

    protected function makeForm($route = '')
    {
        $form = $this->manager->getForm();
        $form->setAttribute(
            'action',
            $route
            );
        return $form;
    }

    public function logoutAction()
    {
        $identity = $this->isAuthenticated();
        if ($identity){
            $this->clearIdentity();
            $this->flashMessenger()->addInfoMessage('ログアウトしました');
        } else {
            $this->flashMessenger()->addInfoMessage('未ログインです');
        }
        return $this->redirect()->toRoute($this->routeSiteTop);
    }

    public function contentsAction()
    {
        if (!$this->isAuthenticated()) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        // ナビゲーションバー用の値をセット
        $this->layout()->setVariable('identity', true);
        $this->layout()->setVariable('navPosition', 'contents');

        return new ViewModel(['user'=>$this->identity]);
    }

}