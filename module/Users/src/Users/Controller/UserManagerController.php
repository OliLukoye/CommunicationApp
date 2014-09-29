<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of UserManagerController
 *
 * @author OliLukoye
 */
class UserManagerController extends AbstractActionController
{
    public function indexAction() 
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $viewModel = new ViewModel(array(
            'users' => $userTable->fetchAll()
        ));
        return $viewModel;
    }
    
    public function editAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUser($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('UserEditForm');
        $form->bind($user);
        $viewModel = new ViewModel(array(
            'form' => $form,
            'user_id' => $this->params()->fromRoute('id')
        ));
        return $viewModel;
    }
    
    public function processAction()
    {
        // Получение идентификтора польщователя из POST
        $post = $this->request->getPost();
        $userTable = $this->getServiceLocator()->get('UserTable');
        // Загрузка сущности User
        $user = $userTable->getUser($post->id);
        // Привязка сущности User к Form
        $form = $this->getServiceLocator()->get('UserEditForm');
        $form->bind($user);
        $form->setData($post);
        if (!$form->isValid()){
            $model = new ViewModel(array(
                'error' => TRUE,
                'form' => $form,
            ));
            $model->setTemplate('users/user-manager/edit');
            return $model;
        }
        
        // Сохранение пользователя
        $userTable->saveUser($user);
            
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'user-manager',
            'action'     => 'index'
        ));
    }
    
    public function deleteAction()
    {
        $this->getServiceLocator()->get('UserTable')
                ->deleteUser($this->params()->fromRoute('id'));
        
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'user-manager',
            'action'     => 'index'
        ));
    }
    
    public function addAction()
    {
        if (!$this->request->isPost()) {
            $form = $this->getServiceLocator()->get('AddUserForm');
            $viewModel = new ViewModel(array(
                'form' => $form,
            ));
            return $viewModel;
        }
        
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('AddUserForm');
        $form->setData($post);
        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => TRUE,
                'form' => $form,
            ));
            $model->setTemplate('users/user-manager/add');
            return $model;
        }
        
        //создание пользователя
        $user = new \Users\Model\User();
        $user->exchangeArray($form->getData());
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userTable->saveUser($user);
        
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'user-manager',
            'action'     => 'index'
        ));
    }
}
