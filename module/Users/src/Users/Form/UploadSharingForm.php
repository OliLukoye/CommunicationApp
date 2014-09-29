<?php
namespace Users\Form;

use Zend\Form\Form;

/**
 * Description of LoginForm
 *
 * @author ADMIN
 */
class UploadSharingForm extends Form
{
    public function __construct($name = null) 
    {
        parent::__construct('UploadSharing');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'upload_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
            'options' => array(
                'label' => 'Upload',
            ),
        ));

        
        $this->add(array(
            'name' => 'user_id',
        	'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
            ),
            'options' => array(
                'label' => 'Choose User',
            ),
        )); 
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add Share'
            ),
        ));
    }
}
