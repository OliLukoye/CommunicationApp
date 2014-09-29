<?php
namespace Users\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of LoginFilter
 *
 * @author ADMIN
 */
class LoginFilter extends InputFilter
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
    }
}
