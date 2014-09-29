<?php
namespace Users\Model;

/**
 * Description of User
 *
 * @author ADMIN
 */
class User 
{
    public $id;
    public $name;
    public $email;
    public $password;
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : NULL;
        $this->name = (isset($data['name'])) ? $data['name'] : NULL;
        $this->email = (isset($data['email'])) ? $data['email'] : NULL;
        if (isset($data['password'])) {
            $this->setPassword($data['password']);
        }
    }
    
    public function getArrayCopy()
    {
        return array(
            'id'     => $this->id,
            'name'   => $this->name,
            'email'  => $this->email,
        );
    }
    
    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }
}
