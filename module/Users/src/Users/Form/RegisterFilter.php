<?php
namespace Users\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of RegisterFilter
 *
 * @author ADMIN
 */
class RegisterFilter extends InputFilter
{
    public function __construct() 
    {
        $this->add(array(
            'name' => 'email',
            'required' => TRUE,
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'domain' => TRUE,
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'name',
            'required' => TRUE,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'utf-8',
                        'min' => 2,
                        'max' => 140,
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'required' => TRUE,
        ));
        
        $this->add(array(
            'name' => 'confirm_password',
            'required' => TRUE,
        ));
    }
}
