<?php

namespace Album\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\Authentication\AuthenticationService;
 use Zend\Session\Container;
 use Zend\View\Model\ViewModel;
 use Album\Model\User;       
 use Album\Form\LoginForm;

 class UserController extends AbstractActionController
 {
    protected $sessionContainer;

    public function __construct()
    {
        $this->sessionContainer = new Container('sessionz');

        $this->sessionContainer->offsetSet('user',array('username'=>'sofakingdom','password'=>'palmer'));
    }

     public function indexAction()
     {

        // grab the paginator from the AlbumTable
     $paginator = $this->getAlbumTable()->fetchAll(true);
     // set the current page to what has been passed in query string, or to 1 if none set
     $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
     // set the number of items per page to 10
     $paginator->setItemCountPerPage(10);

     return new ViewModel(array(
         'paginator' => $paginator
     ));

     }

         
 }