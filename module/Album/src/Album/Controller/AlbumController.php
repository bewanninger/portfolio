<?php

namespace Album\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Album\Model\Album;  
 use Album\Model\Drawing;        
 use Album\Form\AlbumForm;
 use Album\Form\UploadForm;

 class AlbumController extends AbstractActionController
 {
    protected $albumTable;
    protected $drawingTable;

     public function indexAction()
     {
          return new ViewModel(array(
             'albums' => $this->getAlbumTable()->fetchAll(),
         ));

     }

     public function addAction()
     {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);
                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array('form' => $form);
     }

    public function uploadFormAction()
    {
        $form     = new UploadForm('upload-form');
        $tempFile = null;

        $prg = $this->fileprg($form);
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            return $prg; // Return PRG redirect response
        } elseif (is_array($prg)) {
            if ($form->isValid()) {
                //create a copy to send to be saved to DB
                $drawing = new Drawing();
                $data = $form->getData();
                $drawing->fileName = $data["image-file"]["name"];
                $drawing->title = $data["text"];
                //Write it to DB
                //$album->exchangeArray($form->getData());
                $this->getDrawingTable()->saveAlbum($drawing);
                //make the Image viewable
                chmod($data["image-file"]["tmp_name"], 0644);
                // Form is valid, save the form!
                return array(
                'form'     => $form,
                'tempFile' => $tempFile,
                'data'     => $data,
                'foo'      => $drawing,
                );
                //return $this->redirect()->toRoute('album',array('action' =>'success'));
            } else {
                // Form not valid, but file uploads might be valid...
                // Get the temporary file information to show the user in the view
                $fileErrors = $form->get('image-file')->getMessages();
                if (empty($fileErrors)) {
                    $tempFile = $form->get('image-file')->getValue();
                }
            }
        }

        return array(
            'form'     => $form,
            'tempFile' => $tempFile,
        );
    }


    public function galleryAction()
     {
        
        //$dir = "./html/img/upload";
       // $drawingNames = scandir($dir);
        //return array (
            //'drawingNames' => $drawingNames,
            //);
        return new ViewModel(array(
                'drawings' => $this->getDrawingTable()->fetchAll(),
            ));
     }


     public function viewAction()
     {
        //Get the Id from The URL if there is one, otherwise go to gallery
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'gallery'
            ));
        }
        //try to grab the info for the picture id added
        try {
            $drawing = $this->getDrawingTable()->getAlbum($id);
            return array( 'drawing' => $drawing);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'index'
            ));
        }

            
     }

     public function editAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $album = $this->getAlbumTable()->getAlbum($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'index'
            ));
        }

        $form  = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
     }

     public function deleteAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
     }

     public function getAlbumTable()
     {
        $table = "album";
         if (!$this->albumTable) {
             $sm = $this->getServiceLocator();
             $this->albumTable = $sm->get('Album\Model\AlbumTable');
         }
         return $this->albumTable;
     }


    public function getDrawingTable()
         {
            $table = "Drawing";
             if (!$this->drawingTable) {
                 $sm = $this->getServiceLocator();
                 $this->drawingTable = $sm->get('Album\Model\DrawingTable');
             }
             return $this->drawingTable;
         }

 }