<?php
namespace Common\Model;

class IndexManager
{
    /** @var  \Zend\ServiceManager\ServiceManager */
    protected $serviceManager;

    /** @var \Users\Model\UsersTable */
    protected $usersTable;

    /** @var \Common\Form\LoginForm || Common\Form\SignupForm */
    protected $form = null;

    protected $postData;

    /** @var \Common\Authentication\Adapter\ApiAdapter */
    protected $authAdapter;

    /** @var \Zend\Authentication\AuthenticationService */
    protected $authenticationService;

    public function __construct($sm)
    {
        $this->serviceManager = $sm;
    }

    public function setPostData($data)
    {
        $this->postData = $data;
        return $this;
    }
    public function getPostData()
    {
        if (empty($this->postData)) {
            $request = $this->serviceManager->get('Request');
            $this->postData = $request->getPost()->toArray();
        }
        return $this->postData;
    }

    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }
    public function getForm()
    {
        if (is_null($this->form)) {
            throw new \Exception('内部エラー: FormオブジェクトがManagerクラスにセットされていません');
        }
        return $this->form;
    }

    /**
     * @param \Common\Authentication\Adapter\ApiAdapter $adapter
     * @param \Zend\Authentication\AuthenticationService $authenticationService
     */
    public function setAuthAdapter($adapter, $authenticationService)
    {
        $this->authAdapter = $adapter;
        $this->authenticationService = $authenticationService;
    }

    public function checkPostData()
    {
        switch (true) {
            case ($this->getForm()->getSubmitName() === 'login'):
                $ret = $this->checkLoginPost();
                break;
            default:
                throw new \Exception('No keyword');
        }
        return $ret;
    }

    /**
     * @return array
     */
    protected function checkLoginPost()
    {
        $ret = [];
        $data = $this->getPostData();
        $loginForm  = $this->getForm();

        $loginForm->setInputFilter($loginForm->makeInputFilter());
        $loginForm->setData($data);
        if($loginForm->isValid()){
            $ret = [
                'result' => true,
                'message' => null
            ];
        } else {
            _appError(__METHOD__.' Could not pass the verification.', $loginForm->getMessages());
            $ret = [
                'result' => false,
                'message' => ['入力内容を見直してください'],
            ];
        }
        return $ret;
    }

    public function authenticate()
    {
        $postData = $this->getPostData();
        if ($this->authAdapter && $this->authenticationService) {
            // 規定のAdapter経由で認証処理をする
            $authAdapter = $this->authAdapter;
            $authAdapter->setUsername($postData['username'])
                        ->setPassword($postData['password']);
            $auth = $this->authenticationService->setAdapter($authAdapter);
            $authResult = $auth->authenticate();
            $ret = [
                'result' => false,
                'message' => $authResult->getMessages(),
                'user' => $authResult->getIdentity(),
            ];
            if ($authResult->getCode()) {
                $ret['result'] = true;
            } else {
                $mesg = array_merge(['認証を通過できませんでした'], $authResult->getMessages());
                $ret['message'] = $mesg;
            }
            return $ret;
        } else {
            return [
                'result' => false,
                'message' => ['内部エラー: 認証用アダプタが正しくセットされていません'],
            ];
        }

    }
}