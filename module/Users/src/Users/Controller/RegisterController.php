<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\RegisterForm;
use Users\Form\RegisterFilter;
/**
 * Description of RegisterController
 *
 * @author ADMIN
 */
class RegisterController extends AbstractActionController
{
    public function indexAction() 
    {
        $form = new RegisterForm();
        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel;
    }
    
    public function confirmAction()
    {
        return new ViewModel();
    }
    
    public function processAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL, array(
                'controller' => 'register',
                'action' => 'index'
            ));
        }
        
        $post = $this->request->getPost();
        $form = new RegisterForm();
        $inputFilter = new RegisterFilter();
        $form->setInputFilter($inputFilter);
        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => TRUE,
                'form' => $form,
            ));
            $model->setTemplate('users/register/index');
            return $model;
        }
        
        //создание пользователя
        $this->createUser($form->getData());
        
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'register',
            'action' => 'confirm'
        ));
    }
    
    public function createUser(array $data)
    {
        $user = new \Users\Model\User();
        $user->exchangeArray($data);
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userTable->saveUser($user);
        return TRUE;
    }
}
