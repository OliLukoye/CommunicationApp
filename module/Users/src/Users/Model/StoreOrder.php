<?php
namespace Users\Model;

/**
 * Description of StoreOrder
 *
 * @author OliLukoye
 */
class StoreOrder 
{
    public $id;
    public $store_product_id;
    public $qty;
    public $total;
    public $status;
    public $stamp;
    
    public $first_name;
    public $last_name;
    public $email;
    public $ship_to_street;
    public $ship_to_city;
    public $ship_to_state;
    public $ship_to_zip;
    
    protected $_product;
    
    public function __construct(StoreProduct $product = NULL)
    {
        $this->status = 'new';
        
        if (!empty($product)) {
            $this->setProduct($product);
        }
    }

    public function exchangeArray($data)
    {
        $this->id =  (isset($data['id'])) ? $data['id'] : NULL;
        $this->store_product_id =  (isset($data['store_product_id'])) ? $data['store_product_id'] : NULL;
        $this->qty =  (isset($data['qty'])) ? $data['qty'] : NULL;
        $this->total =  (isset($data['total'])) ? $data['total'] : NULL;
        $this->status =  (isset($data['status'])) ? $data['status'] : NULL;
        $this->stamp =  (isset($data['stamp'])) ? $data['stamp'] : NULL;
        $this->first_name =  (isset($data['first_name'])) ? $data['first_name'] : NULL;
        $this->last_name =  (isset($data['last_name'])) ? $data['last_name'] : NULL;
        $this->email =  (isset($data['email'])) ? $data['email'] : NULL;
        $this->ship_to_street =  (isset($data['ship_to_street'])) ? $data['ship_to_street'] : NULL;
        $this->ship_to_city =  (isset($data['ship_to_city'])) ? $data['ship_to_city'] : NULL;
        $this->ship_to_state =  (isset($data['ship_to_state'])) ? $data['ship_to_state'] : NULL;
        $this->ship_to_zip =  (isset($data['ship_to_zip'])) ? $data['ship_to_zip'] : NULL;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function setProduct (StoreProduct $product)
    {
        $this->_product = $product;
        $this->store_product_id = $product->id;
    }
    
    public function getProduct()
    {
        return $this->_product;
    }
    
    public function calculateSubTotal()
    {
        if (NULL === $this->_product) {
            return 0;
        } else {
            $this->total = $this->qty * $this->_product->cost;
            return $this->total;
        }
    }
    
    public function setQuantity ($quantity)
    {
        $this->qty = $quantity;
        if (!empty($this->_product)) {
            $this->calculateSubTotal();
        }
    }
}
