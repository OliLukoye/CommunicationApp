<?php
namespace Users\Form;

use Zend\Form\Form;

/**
 * Description of UploadForm
 *
 * @author OliLukoye
 */
class UploadForm extends Form
{
    public function __construct($name = null) 
    {
        parent::__construct('Upload');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');
        
        $this->add(array(
            'name' => 'fileupload',
            'attributes' => array(
                'type' => 'file',
            ),
            'options' => array(
                'label' => 'File Upload',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Upload'
            ),
        ));
        $this->add(array(
            'name' => 'label',
            'attributes' => array(
                'type' => 'hidden',
                'value' => 'Corporate Report'
            ),
        ));
    }
}
