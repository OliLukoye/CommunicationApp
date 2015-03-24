<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Users\Form\UploadForm;
use Zend\Debug\Debug;

/**
 * Description of MediaManagerController
 *
 * @author olilukoye
 */
class MediaManagerController extends AbstractActionController
{
    protected $authservice;
    
    protected $photos;
	
//    const GOOGLE_USER_ID = 'zf2.book@gmail.com';
//    const GOOGLE_PASSWORD = 'pa$$w0rd';
    const GOOGLE_USER_ID = 'tstmailmy@gmail.com';
    const GOOGLE_PASSWORD = 'dtcyfghtikf';
    
    protected function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }
    
    public function indexAction() 
    {
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $userTable = $this->getServiceLocator()->get('UserTable');
        // Получение информации о пользователе от сеанса
        $userEmail = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);
        $googleAlbums = $this->getGooglePhotos();
        //$youtubeVideos = $this->getYoutubeVideos();
        
        $viewModel = new ViewModel(array(
            'myImageFileUploads' => $uploadTable->getUploadsByUserId($user->id),
            'googleAlbums' => $googleAlbums,
            /*'youtubeVideos' => $youtubeVideos,*/
        ));
        
        return $viewModel;
    }
    
    public function viewAction()
    {
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        
        $viewModel = new ViewModel(array(
            'upload' => $uploadTable->getUpload($this->params()->fromRoute('id')),
            'action' => $this->params()->fromRoute('subaction'),
        ));
        
        return $viewModel;
    }
    
    public function uploadAction()
    {
        $form = new UploadForm();
        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel;
    }
    
    public function getImageUploadLocation()
    {
        // Получение информации из конфигурационных данных модуля
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['image_upload_location'];
    }

    public function processAction()
    {
        $uploadFile = $this->params()->fromFiles('fileupload');
        $form = new UploadForm();
        $form->setData($this->request->getPost());
        if ($form->isValid()) {
            // Получение конфигурации из конфигурационных данных модуля
            $uploadPath = $this->getImageUploadLocation();
            // Получение информации о пользователе от сеанса
            $userTable = $this->getServiceLocator()->get('UserTable');
            $userEmail = $this->getAuthService()->getStorage()->read();
            $user = $userTable->getUserByEmail($userEmail);
            
            // Сохранение выгруженного файла
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $adapter->setDestination($uploadPath);
            if ($adapter->receive($uploadFile['name'])) {
                // Успешная выгрузка файла
                // Создаем экскиз
                $thumbnailFileName = $this->generateThumbnail($uploadFile['name']);
                $exchange_data = array();
                $exchange_data['label'] = $this->request->getPost()->get('label');
                $exchange_data['filename'] = $uploadFile['name'];
                $exchange_data['thumbnail'] = $thumbnailFileName;
                $exchange_data['user_id'] = $user->id;
                
                $imageUpload = new \Users\Model\ImageUpload();
                $imageUpload->exchangeArray($exchange_data);
                $imageUploadTable = $this->getServiceLocator()->get('ImageUploadTable');
                $imageUploadTable->saveUpload($imageUpload);
                
                return $this->redirect()->toRoute(NULL, array(
                    'controller' => 'MediaManager',
                    'action'     => 'index',
                ));
            } else {
                $model = new ViewModel(array(
                    'error' => TRUE,
                    'form' => $form,
                ));
                $model->setTemplate('users/media-manager/upload');
                return $model;
            }
        }
    }
    
    public function generateThumbnail ($imageFileName)
    {
        $path = $this->getImageUploadLocation();
        $sourceImageFileName = $path . '/' . $imageFileName;
        $thumbnailFileName = 'tn_' . $imageFileName;
        
        $imageThumb = $this->getServiceLocator()->get('WebinoImageThumb');
        $thumb = $imageThumb->create($sourceImageFileName, $options = array());
        $thumb->resize(75, 75);
        $thumb->save($path . '/' . $thumbnailFileName);
        
        return $thumbnailFileName;
    }
    
    public function showImageAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $upload = $uploadTable->getUpload($uploadId);
        
        // Выборка конфигурации из модуля
        $uploadPath = $this->getImageUploadLocation();
        if ($this->params()->fromRoute('subaction') == 'thumb') {
            $filename = $uploadPath . '/' . $upload->thumbnail;
        } else {
            $filename = $uploadPath . '/' . $upload->filename;
        }
        $file = file_get_contents($filename);
        
        // Прямой возврат ответа
        $response = $this->getEvent()->getResponse();
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename=' . $upload->filename,
        ));
        $response->setContent($file);
        return $response;
    }
    
    public function rotateImageAction()
    {
        $uploadId = $this->params()->fromRoute('id');
        $uploadTable = $this->getServiceLocator()->get('ImageUploadTable');
        $upload = $uploadTable->getUpload($uploadId);
        
        // Выборка конфигурации из модуля
        $uploadPath = $this->getImageUploadLocation();
        $sourceImageFileName = $uploadPath . '/' . $upload->filename;
        $imageThumb = $this->getServiceLocator()->get('WebinoImageThumb');
        $thumb = $imageThumb->create($sourceImageFileName, $options = array());
        if ($this->params()->fromRoute('subaction') == 'cw') {
            $thumb->rotateImage('CW');
        } else {
            $thumb->rotateImage('CCW');
        }
        $thumb->show();
    }
    
    public function getGooglePhotos()
    {
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter->setOptions(array(
            'curloptions' => array(
                CURLOPT_SSL_VERIFYPEER => FALSE,
            )
        ));
        
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        
        try {
            $client = \ZendGData\ClientLogin::getHttpClient(
                    self::GOOGLE_USER_ID, 
                    self::GOOGLE_PASSWORD,
                    \ZendGData\Photos::AUTH_SERVICE_NAME,
                    $httpClient);
        } catch (\ZendGData\App\AuthException $exc) {
            Debug::dump($exc->getTraceAsString(), 'Ошибка гугла (auth_exp): '); 
        } catch (\ZendGData\App\HttpException $e) {
            Debug::dump($e->getTraceAsString(), 'Ошибка гугла(php http_exc): '); 
        } catch (\ZendGData\App\Exception $e) {
            Debug::dump($e->getTraceAsString(), 'Ошибка гугла(php exc): '); 
        }

        $gp = new \ZendGData\Photos($client);
        
        $userFeed = $gp->getUserFeed(self::GOOGLE_USER_ID);
        
        $gAlbums = array();
        foreach ($userFeed as $userEntry) {
            $albumId = $userEntry->getGphotoId()->getText();
            $gAlbums[$albumId]['label'] = $userEntry->getTitle()->getText();
            
            $query = $gp->newAlbumQuery();
            $query->setUser(self::GOOGLE_USER_ID);
            $query->setAlbumId($albumId);
            
            $albumFeed = $gp->getAlbumFeed($query);
            
            foreach ($albumFeed as $photoEntry) {
                $photoId = $photoEntry->getGphotoId()->getText();
                if ($photoEntry->getMediaGroup()->getContent() != NULL) {
                    $mediaContentArray = $photoEntry->getMediaGroup()->getContent();
                    $photoUrl = $mediaContentArray[0]->getUrl();
                }
                
                if ($photoEntry->getMediaGroup()->getThumbnail() != NULL) {
                    $mediaThumbnailArray = $photoEntry->getMediaGroup()->getThumbnail();
                    $thumbUrl = $mediaThumbnailArray[0]->getUrl();
                }
                
                $albumPhoto = array();
                $albumPhoto['id'] = $photoId;
                $albumPhoto['photoUrl'] = $photoUrl;
                $albumPhoto['thumbUrl'] = $thumbUrl;
                
                $gAlbums[$albumId]['photos'][] = $albumPhoto;
            }
        }
        
        return $gAlbums;
    }
    
    public function getYoutubeVideos()
    {
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter->setOptions(array(
            'curloptions' => array(
                CURLOPT_SSL_VERIFYPEER => FALSE,
            )
        ));
        
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        
        try {
            $client = \ZendGData\ClientLogin::getHttpClient(
                    self::GOOGLE_USER_ID, 
                    self::GOOGLE_PASSWORD,
                    \ZendGData\YouTube::AUTH_SERVICE_NAME,
                    $httpClient);
        } catch (\ZendGData\App\AuthException $exc) {
            Debug::dump($exc->getTraceAsString(), 'Ошибка гугла (auth_exp): '); 
        } catch (\ZendGData\App\HttpException $e) {
            Debug::dump($e->getTraceAsString(), 'Ошибка гугла(php http_exc): '); 
        } catch (\ZendGData\App\Exception $e) {
            Debug::dump($e->getTraceAsString(), 'Ошибка гугла(php exc): '); 
        }
        
        $yt = new \ZendGData\YouTube($client);
        $yt->setMajorProtocolVersion(2);
        $query = $yt->newVideoQuery();
//        $query->setOrderBy('relevance');
//        $query->setSafeSearch('none');
        $query->setOrderBy('viewCount');
        $query->setSafeSearch('none');
        $query->setVideoQuery('Zend Framework');
        
        $videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));
        
        $yVideos = array();
        foreach ($videoFeed as $videoEntry) {
            $yVideo = arrra();
            $yVideo['videoTitle'] = $videoEntry->getVideoTitle();
            $yVideo['videoDescription'] = $videoEntry->getVideoDescription();
            $yVideo['watchPage'] = $videoEntry->getVideoWatchPageUrl();
            $yVideo['duration'] = $videoEntry->getVideoDuration();
            $videoThumbnails = $videoEntry->getVideoThumbnails();
            $yVideo['thumbnailUrl'] = $videoThumbnails[0]['url'];
            $yVideos[] = $yVideo;
        }
        
        return $yVideos;
    }
}
