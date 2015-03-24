<?php
namespace Users\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Description of StoreProductTable
 *
 * @author OliLukoye
 */
class StoreProductTable 
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway) 
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function saveProduct(StoreProduct $product)
    {
        $data = array(
            'name' => $product->name,
            'desc' => $product->desc,
            'cost' => $product->cost
        );
        
        $id = (int)$product->id;
        if (0 == $id) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getProduct($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new Exception('Обновляемая запись не найдена.');
            }
        }
    }
    
    public function getProduct($productId)
    {
        $id = (int)$productId;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new Exception("Запись с номером $id не найдена.");
        }
        return $row;
    }
    
    public function deleteProduct($productId)
    {
        $id = (int)$productId;
        $this->tableGateway->delete(['id' => $id]);
    }
}
