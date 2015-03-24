<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Description of StoreOrderTable
 *
 * @author OliLukoye
 */
class StoreOrderTable
{
    protected $tableGateway;
    protected $productTableGateway;
    
    public function __construct(TableGateway $tableGateway, TableGateway $productTableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->productTableGateway = $productTableGateway;
    }
    
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }
    
    public function saveOrder(StoreOrder $order)
    {
        $data = array(
            'store_product_id' => $order->store_product_id,
            'qty' => $order->qty,
            'total' => $order->total,
            'status' => $order->status,
            'stamp' => $order->stamp,
            'first_name' => $order->first_name,
            'last_name' => $order->last_name,
            'email' => $order->email,
            'ship_to_street' => $order->ship_to_street,
            'ship_to_city' => $order->ship_to_city,
            'ship_to_state' => $order->ship_to_state,
            'ship_to_zip' => $order->ship_to_zip
        );
        
        $id = (int)$order->id;
        if (0 == $id) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getOrder($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new Exception('Обновляемая запись не найдена.');
            }
        }
    }
    
    public function getOrder($orderId)
    {
        $id = (int)$orderId;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $order = $rowset->current();
        if (!$order) {
            throw new Exception("Запись с номером $id не найдена.");
        }
        
        $productId = $order->store_product_id;
        
        $prodRowset = $this->productTableGateway->select(['id' => $productId]);
        $product = $prodRowset->current();
        
        if (!empty($product)) {
            $order->setProduct($product);
        }
        
        return $order;
    }
    
    public function deleteOrder($orderId)
    {
        $id = (int)$orderId;
        $this->tableGateway->delete(['id' => $id]);
    }
}
