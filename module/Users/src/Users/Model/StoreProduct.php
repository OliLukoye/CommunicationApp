<?php
namespace Users\Model;

/**
 * Description of StoreProduct
 *
 * @author OliLukoye
 */
class StoreProduct 
{
    public $id;
    public $name;
    public $desc;
    public $cost;
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : NULL;
        $this->name = (isset($data['name'])) ? $data['name'] : NULL;
        $this->desc = (isset($data['desc'])) ? $data['desc'] : NULL;
        $this->cost = (isset($data['cost'])) ? $data['cost'] : NULL;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
