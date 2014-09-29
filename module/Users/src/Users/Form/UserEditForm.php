<?php
namespace Users\Form;

use Zend\Form\Form;

/**
 * Description of UserEditForm
 *
 * @author OliLukoye
 */
class UserEditForm extends Form
{
    public function __construct($name = null) 
    {
        parent::__construct('UserEdit');
        $this->setAttribute('method', 'post');
        //$this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Full Name',
            ),
        ));
        
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
            //'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Edit'
            ),
        ));
    }
}
