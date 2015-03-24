<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Users\Form\UploadForm;
//use Zend\Debug\Debug;

/**
 * Description of UploadManagerController
 *
 * @author OliLukoye
 */
class UploadManagerController extends AbstractActionController
{
    protected $authservice;
    
    public function indexAction() 
    {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $userTable = $this->getServiceLocator()->get('UserTable');
        // Получение информации о пользователе от сеанса
        //Debug::dump($this->getAuthService()->getStorage()->read(), 'Аутентификация: ');
        $userEmail = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);
        $sharingUploads = $uploadTable->getSharedUploadsForUserId($user->id);
        foreach ($sharingUploads as $sharingUpload) {
            $uploadOwner = $userTable->getUser($sharingUpload->user_id);
            $sharingUploadInfo = array();
            $sharingUploadInfo['label'] = $sharingUpload->label;
            $sharingUploadInfo['filename'] = $sharingUpload->filename;
            $sharingUploadInfo['owner'] = $uploadOwner->name;
            $sharingUploadList[$sharingUpload->id] = $sharingUploadInfo;
        }
        
        $viewModel = new ViewModel(array(
            'myUploads' => $uploadTable->getUploadsByUserId($user->id),
            'sharingUploads' => $sharingUploadList,
        ));
        //Debug::dump($viewModel, 'Выходные данные: ');
        return $viewModel;
    }
    
    public function uploadAction()
    {
        $form = new UploadForm();
        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel;
    }
    
    public function editAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userEmail = $this->getAuthService()->getStorage()->read();
        // Добавляем данные о доступах
        $uploadSharingForm = $this->getServiceLocator()->get('UploadSharingForm');
        $users = $userTable->fetchAll();
        $aUsers = array();
        foreach ($users as $user) {
            if ($userEmail != $user->email) {
                $aUsers[$user->id] = $user->name;
            }
        }
//        $hidden = new \Zend\Form\Element\Hidden('upload_id');
//        $hidden->setValue($uploadId);
//        $select = new \Zend\Form\Element\Select('user_id');
//        $select->setLabel('Choose User');
//        $select->setValueOptions($aUsers);
        $uploadSharingForm->get('upload_id')->setValue($uploadId);
        $uploadSharingForm->get('user_id')->setValueOptions($aUsers);
        
        $user = $userTable->getUserByEmail($userEmail);
        $file = $uploadTable->getUpload($uploadId);
        $sharedUsers = $uploadTable->getSharedUsers($uploadId);
        //Debug::dump($file, 'Инфа о файле: '); 
        //Debug::dump($sharedUsers, 'Инфа о пользователях: '); //TODO: Выводить список пользователей которым дан доступ к выгруженным файлам
        
        $viewModel = new ViewModel(array(
            'userId' => $user->id,
            'uploadFile' => $file,
            'sharedUsers' => $sharedUsers,
            'form' => $uploadSharingForm,
        ));
        
        return $viewModel;
    }
    
    //TODO: Delete sharing user
    public function deleteSharedAction()
    {
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'UploadManager',
            'action'     => 'edit',
            'id'         => ''
        ));
    }

    public function addSharingAction()
    {
        //$userTable = $this->getServiceLocator()->get('UserTable');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        //$form = new \Users\Form\UploadSharingForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $userId = $request->getPost()->get('user_id');
            $uploadId = $request->getPost()->get('upload_id');
            $uploadTable->addSharing($uploadId, $userId);
        }
        
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'UploadManager', 
            'action' => 'edit', 
            'id' => $request->getPost()->get('upload_id')
        ));
    }

    public function deleteAction()
    {
        $uploadPath = $this->getFileUploadLocation();
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $file = $uploadTable->getUpload($this->params()->fromRoute('id'));
        unlink($uploadPath . DIRECTORY_SEPARATOR . $file->filename);
        $uploadTable->deleteUpload($this->params()->fromRoute('id'));
        
        return $this->redirect()->toRoute(NULL, array(
            'controller' => 'UploadManager',
            'action'     => 'index'
        ));
    }
    
    public function fileDownloadAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $upload = $uploadTable->getUpload($uploadId);
        
        // Считывание конфигурации из модуля
        $uploadPath = $this->getFileUploadLocation();
//        $file = file_get_contents($uploadPath . "/" . $upload->filename);
//        
//        // Непосредственное возвращение ответа
//        $response = $this->getEvent()->getResponse();
//        $response->getHeaders()->addHeaders(array(
//            'Content-Type' => 'application/octet-stream',
//            'Content-Disposition' => 'attachment;filename="' . $upload->filename . '"',
//        ));
//        $response->setContent($file);
//        return $response;
        
        $file = $uploadPath . "/" . $upload->filename;
        
        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($file, 'r'));
        $response->setStatusCode(200);
        $response->setStreamName(basename($file));

        $headers = new \Zend\Http\Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . basename($file) .'"',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => filesize($file)
        ));
        $response->setHeaders($headers);
        return $response;
    }
    
    public function processAction()
    {
        $uploadFile = $this->params()->fromFiles('fileupload');
        $form = new UploadForm();
        $form->setData($this->request->getPost());
        if ($form->isValid()) {
            // Получение конфигурации из конфигурационных данных модуля
            $uploadPath = $this->getFileUploadLocation();
            // Получение информации о пользователе от сеанса
            $userTable = $this->getServiceLocator()->get('UserTable');
            $userEmail = $this->getAuthService()->getStorage()->read();
            $user = $userTable->getUserByEmail($userEmail);
            
            // Сохранение выгруженного файла
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $adapter->setDestination($uploadPath);
            if ($adapter->receive($uploadFile['name'])) {
                // Успешная выгрузка файла
                $exchange_data = array();
                $exchange_data['label'] = $this->request->getPost()->get('label');
                $exchange_data['filename'] = $uploadFile['name'];
                $exchange_data['user_id'] = $user->id;
                
                $upload = new \Users\Model\Upload();
                $upload->exchangeArray($exchange_data);
                $uploadTable = $this->getServiceLocator()->get('UploadTable');
                $uploadTable->saveUpload($upload);
                
                return $this->redirect()->toRoute(NULL, array(
                    'controller' => 'UploadManager',
                    'action'     => 'index',
                ));
            } else {
                $model = new ViewModel(array(
                    'error' => TRUE,
                    'form' => $form,
                ));
                $model->setTemplate('users/upload-manager/upload');
                return $model;
            }
        }
    }
    
    public function getFileUploadLocation()
    {
        // Получение информации из конфигурационных данных модуля
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }

    public function getAuthService()
    {
        if (!$this->authservice) {
            $authService = new AuthenticationService();
            $this->authservice = $authService;
        }
        return $this->authservice;
    }
}
