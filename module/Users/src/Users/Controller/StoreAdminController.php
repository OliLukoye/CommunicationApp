<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of StoreAdminController
 *
 * @author OliLukoye
 */
class StoreAdminController extends AbstractActionController
{
    public function indexAction() 
    {
        $storeProductTable = $this->getServiceLocator()->get('StoreProductTable');
        $storeProducts = $storeProductTable->fetchAll();
        
        $viewModel = new ViewModel([
            'storeProducts' => $storeProducts
        ]);
        return $viewModel;
    }
    
    public function addProductAction() {}
    public function deleteProductAction() {}
    public function listOrdersAction() {}
    public function viewOrderAction() {}
    public function updateOrderStatusAction() {}
            
}
