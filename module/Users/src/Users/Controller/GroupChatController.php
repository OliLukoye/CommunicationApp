<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of GroupChatController
 *
 * @author OliLukoye
 */
class GroupChatController extends AbstractActionController
{
    protected $authservice;
    
    protected function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }

    protected function getLoggedInUser()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userEmail = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);
        
        return $user;
    }
    
    public function indexAction() 
    {
        $user = $this->getLoggedInUser();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $messageText = $request->getPost()->get('message');
            $fromUserId = $user->id;
            $this->sendMessage($messageText, $fromUserId);
            // Для предотварщения дублирования записей при обновлении
            return $this->redirect()->toRoute('users/group-chat');
        }
        
        // Подготовка формы отправки сообщения
        $form = new \Zend\Form\Form();
        $form->add(array(
            'name' => 'message',
            'attributes' => array(
                'type' => 'text',
                'id' => 'messageText',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Message',
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Send'
            ),
        ));
        $form->add(array(
            'name' => 'refresh',
            'attributes' => array(
                'type' => 'button',
                'id' => 'btnRefresh',
                'value' => 'Refresh'
            ),
        ));
        
        $viewModel = new ViewModel(array(
            'form' => $form,
            'userName' => $user->name
        ));
        
        return $viewModel;
    }
    
    public function messageListAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $chatMessageTG = $this->getServiceLocator()->get('ChatMessagesTableGateway');
        $chatMessages = $chatMessageTG->select();
        
        $messageList = array();
        foreach ($chatMessages as $chatMessage) {
            $formUser = $userTable->getUser($chatMessage->user_id);
            $messageData = array();
            $messageData['user'] = $formUser->name;
            $messageData['time'] = $chatMessage->stamp;
            $messageData['data'] = $chatMessage->message;
            $messageList[] = $messageData;
        }
        
        $viewModel = new ViewModel(array(
            'messageList' => $messageList
        ));
        $viewModel->setTemplate('users/group-chat/message-list');
        $viewModel->setTerminal(TRUE);
        return $viewModel;
    }
    
    protected function sendMessage($messageText, $fromUserId)
    {
        $chatMessageTG = $this->getServiceLocator()->get('ChatMessagesTableGateway');
        $data = array(
            'user_id' => $fromUserId,
            'message' => $messageText,
            'stamp' => NULL
        );
        $chatMessageTG->insert($data);
        return TRUE;
    }
    
    public function sendOfflineMessageAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $allUsers = $userTable->fetchAll();
        $authUser = $this->getLoggedInUser();
        $usersList = array();
        foreach ($allUsers as $user) {
            if ($authUser->email != $user->email) {
                $usersList[$user->id] = $user->name . '(' . $user->email . ')';
            }
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $msgSubj = $request->getPost()->get('messageSubject');
            $msgText = $request->getPost()->get('message');
            $toUser = $request->getPost()->get('toUserId');
            $fromUser = $user->id;
            $this->sendOfflineMessage($msgSubj, $msgText, $fromUser, $toUser);
            // Для предотварщения дублирования записей при обновлении
            return $this->redirect()->toRoute('users/group-chat', array('action' => 'sendOfflineMessage'));
        }
        
        // Подготовка формы отправки сообщения
        $form = new \Zend\Form\Form();
        $form->setAttribute('method', 'POST');
        $form->setAttribute('enctype', 'multipart/form-data');
        $form->add(array(
            'name' => 'toUserID',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type' => 'select'
            ),
            'options' => array(
                'label' => 'To User',
            ),
        ));
        $form->add(array(
            'name' => 'messageSubject',
            'attributes' => array(
                'type' => 'text',
                'id' => 'messageSubject',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Subject',
            ),
        ));
        $form->add(array(
            'name' => 'message',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'message',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Message',
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Send'
            ),
            'options' => array(
                'label' => 'Send'
            ),
        ));
    }
    
    protected function sendOfflineMessage($msgSubj, $msgText, $fromUserId, $toUserId)
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        
        $fromUser = $userTable->getUser($fromUserId);
        $toUser = $userTable->getUser($toUserId);
        
        $mail = new \Zend\Mail\Message();
        $mail->setFrom($fromUser->email, $fromUser->name);
        $mail->addTo($toUser->email, $toUser->name);
        $mail->setSubject($msgSubj);
        $mail->setBody($msgText);
        
        $transport = new \Zend\Mail\Transport\Sendmail();
        $transport->send($mail);
        
        return TRUE;
    }
}
