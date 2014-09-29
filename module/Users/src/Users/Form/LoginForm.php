<?php
namespace Users\Form;

use Zend\Form\Form;

/**
 * Description of LoginForm
 *
 * @author ADMIN
 */
class LoginForm extends Form
{
    public function __construct($name = null) 
    {
        parent::__construct('Login');
        $this->setAttribute('method', 'post');
        //$this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'required' => 'required',
            ),
            'filtres' => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Email adddress format is invalid'
                        )
                    )
                )
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login'
            ),
        ));
    }
}
