<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use Zend\Debug\Debug;

/**
 * Description of SearchController
 *
 * @author OliLukoye
 */
class SearchController extends AbstractActionController
{
    public function indexAction() 
    {
        $request = $this->getRequest();
        $searchResults = array();
        if ($request->isPost()) {
//            setlocale(LC_CTYPE, 'ru_RU.UTF-8');
            setlocale(LC_ALL, 'ru_RU.UTF-8');
            Lucene\Search\QueryParser::setDefaultEncoding('utf-8');
            Lucene\Analysis\Analyzer\Analyzer::getDefault(new Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive());
            $queryText = $request->getPost()->get('query');
            $searchIndexLocation = $this->getIndexLocation();
            $index = Lucene\Lucene::open($searchIndexLocation);
            $searchResults = $index->find($queryText);
            Debug::dump([$searchResults, $queryText]);
        }
        
        // Подготовка формы поиска
        $form = new \Zend\Form\Form();
        $form->add(array(
            'name' => 'query',
            'attributes' => array(
                'type' => 'text',
                'id' => 'queryText',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Search String',
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search'
            ),
        ));
        
        $viewModel = new ViewModel(array(
            'form' => $form,
            'searchResults' => $searchResults
        ));
        return $viewModel;
    }
    
    public function generateIndexAction()
    {
//        setlocale(LC_CTYPE, 'ru_RU.UTF-8');
        setlocale(LC_ALL, 'ru_RU.UTF-8');
        Lucene\Search\QueryParser::setDefaultEncoding('utf-8');
        Lucene\Analysis\Analyzer\Analyzer::getDefault(new Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive());
        
        $searchIndexLocation = $this->getIndexLocation();
        $index = Lucene\Lucene::create($searchIndexLocation);
        
        $userTable = $this->getServiceLocator()->get('UserTable');
        $uploadTable = $this->getServiceLocator()->get('UploadTable');
        $allUploads = $uploadTable->fetchAll();
        foreach ($allUploads as $fileUpload) {
            $uploadOwner = $userTable->getUser($fileUpload->user_id);
            
            // создание полей lucene
            $fileUploadId = Document\Field::unIndexed('upload_id', $fileUpload->id);
            $label = Document\Field::text('label', $fileUpload->label);
            $owner = Document\Field::text('owner', $uploadOwner->name);
            
            // создание нового документа и добавление всех полей
            if (substr_compare($fileUpload->filename,
                    '.xlsx', 
                    strlen($fileUpload->filename) - strlen('.xlsx'),
                    strlen('.xlsx')) === 0) {
                // Индексирование таблицы excel
                $uploadPath = $this->getFileUploadLocation();
                $indexDoc = Lucene\Document\Xlsx::loadXlsxFile($uploadPath . '/' . $fileUpload->filename);
            } elseif (substr_compare($fileUpload->filename, 
                    '.docx', 
                    strlen($fileUpload->filename) - strlen('.docx'),
                    strlen('.docx')) === 0) {
                // Индексирование документа Word
                $uploadPath = $this->getFileUploadLocation();
                $indexDoc = Lucene\Document\Docx::loadDocxFile($uploadPath . '/' . $fileUpload->filename);
            } else {
                $indexDoc = new Lucene\Document();
            }
            $indexDoc->addField($label);
            $indexDoc->addField($owner);
            $indexDoc->addField($fileUploadId);
            $index->addDocument($indexDoc);
        }
        $index->commit();
    }

    public function getIndexLocation()
    {
        // Выборка конфигурации из конфигурационных данных модуля
        $config = $this->getServiceLocator()->get('config');
        if ($config instanceof \Traversable) {
            $config = \Zend\Stdlib\ArrayUtils::iteratorToArray($config);
        }
        if (!empty($config['module_config']['search_index'])) {
            return $config['module_config']['search_index'];
        } else {
            return FALSE;
        }
    }
    
    public function getFileUploadLocation()
    {
        // Получение информации из конфигурационных данных модуля
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }
}
