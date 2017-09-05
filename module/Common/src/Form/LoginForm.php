<?php
namespace Common\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class LoginForm extends Form
{
    protected $routeName = 'site-login';
    protected $submitName = 'login';

    public function getSubmitName()
    {
        return $this->submitName;
    }

    public function __construct($name = null)
    {
        $name = is_null($name) ? $this->routeName : $name;
        parent::__construct($name);

        $this->setAttribute('method', 'post')
             ->setAttribute('class', 'form-horizontal');

        $this->makeElements();
    }

    protected function makeElements(){

        $this->add([
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'class'=>'form-control',
                'placeholder'=>'username',
                'tabindex' => 1,
            ],
            'options' => [
                'label' => 'Username',
                'label_attributes' => [
                    'class' => 'control-label col-sm-2',
                ],
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'class'=>'form-control',
                'placeholder'=>'password',
                'tabindex' => 2,
            ],
            'options' => [
                'label' => 'Password',
                'label_attributes' => [
                    'class' => 'control-label col-sm-2',
                ],
            ],
        ]);

        $this->add(new Element\Csrf('csrf'));
        $this->add([
            'name' => $this->getSubmitName(),
            'attributes' => [
                'id' => 'form-submit',
                'type' => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-primary',
            ],
        ]);

    }

    /**
     * ログイン時のフィルタ＆バリデータ
     * @return \Zend\InputFilter\InputFilter
     */
    public function makeInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        // username のフィルタ
        $inputFilter->add($factory->createInput([
            'name' => 'username',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 3,
                        'max' => 5,
                    ],
                ],
            ],
        ]));

        $inputFilter->add($factory->createInput([
            'name' => 'password',
            'required' => true,
            'filters' => [
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                ],
            ],
        ]));

        return $inputFilter;
    }

}