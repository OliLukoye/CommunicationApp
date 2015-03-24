<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of StoreController
 *
 * @author OliLukoye
 */
class StoreController extends AbstractActionController
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
    
    public function productDetailAction() 
    {
        $productId = $this->params()->fromRoute('id');
        $storeProductTable = $this->getServiceLocator()->get('StoreProductTable');
        $storeProduct = $storeProductTable->getProduct($productId);
        
        //Форма добавления товара в корзину
        $form = new \Zend\Form\Form();
        $form->add(array(
            'name' => 'qty',
            'attributes' => array(
                'type' => 'text',
                'id' => 'qty',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Кол-во'
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'В корзину'
            ),
        ));
        $form->add(array(
            'name' => 'store_product_id',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $storeProduct->id
            ),
        ));
        
        $viewModel = new ViewModel([
            'storeProduct' => $storeProduct,
            'form' => $form
        ]);
        return $viewModel;
    }
    
    public function shoppingCartAction()
    {
        $request = $this->getRequest();
        
        $productId = $request->getPost()->get('store_product_id');
        $quantity = $request->getPost()->get('qty');
        
        $orderTable = $this->getServiceLocator()->get('StoreOrderTable');
        $productTable = $this->getServiceLocator()->get('StoreProductTable');
        $product = $productTable->getProduct($productId);
        
        $newOrder = new \Users\Model\StoreOrder($product);
        $newOrder->setQuantity($quantity);
        
        $orderId = $orderTable->saveOrder($newOrder);
        
        $order = $orderTable->getOrder($orderId);
        $viewModel = new ViewModel(array(
            'order' => $order,
            'productId' => $order->getProduct()->id,
            'productName' => $order->getProduct()->name,
            'productQty' => $order->qty,
            'unitCost' => $order->getProduct()->cost,
            'total' => $order->total,
            'orderId' => $order->id,
        ));
        return $viewModel;
    }
    
    public function paypalExpressCheckoutAction() {}
    public function paymentConfirmAction() {}
    public function paymentCancelAction() {}
    
}
