<?php

namespace Album\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\Authentication\AuthenticationService;
 use Zend\Session\Container;
 use Zend\View\Model\ViewModel;
 use Album\Model\Album;  
 use Album\Model\Drawing;
 use Album\Model\User;        
 use Album\Form\AlbumForm;
 use Album\Form\UploadForm;
 use Album\Form\LoginForm;
 use Zend\Crypt\Password\Bcrypt;

        use Zend\Mail\Message;
        use Zend\Mail\Transport\Smtp as SmtpTransport;
        use Zend\Mime\Message as MimeMessage;
        use Zend\Mime\Part as MimePart;
        use Zend\Mail\Transport\SmtpOptions;


 class AlbumController extends AbstractActionController
 {
    protected $albumTable;
    protected $drawingTable;
    protected $userTable;
    protected $sessionContainer;

    public function __construct()
    {
        $this->sessionContainer = new Container('album');
        $this->sessionContainer->offsetSet("Name",
            ($this->sessionContainer->offsetExists('Name')) ? 
            $this->sessionContainer->offsetGet("Name") : "Guest");
        $this->sessionContainer->offsetSet("UserId",
            ($this->sessionContainer->offsetExists('UserId')) ? 
            $this->sessionContainer->offsetGet("UserId") : 999);
    }

     public function indexAction()
     {
        //return $this->redirect()->toRoute('album',array('action'=>'gallery'));
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

     public function loginAction()
     {

        $form = new LoginForm();
        $request = $this->getRequest();

        if ($request->isPost()) { //check the username and pass if post
            try { 
            $this->checkLogin($request);
            }
            catch (\Exception $e){
            }

            //return a new form if checkLogin was not successful
            return array('form' => $form,
                          'name' => $this->sessionContainer->offsetGet("Name"),
                          'errorMessageClass' => 'alert alert-danger',
                          );
        }
        return array('form' => $form,
                     'name' => $this->sessionContainer->offsetGet("Name"),
                     'errorMessageClass' => 'hidden',
                     );
     }

    

    public function uploadFormAction()
    {
        //Check to see if the user is logged in, if not redirect to Login Page
        if (!$this->userLoggedIn()){
            return $this->redirect()->toRoute('album',array('action'=>'login'));
        }
        
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
            'status' => $this->userLoggedIn(),
            'name' => $this->sessionContainer->offsetGet("Name"),
        );
    }


    public function galleryAction()
     {
        
        //$dir = "./html/img/upload";
       // $drawingNames = scandir($dir);
        //return array (
            //'drawingNames' => $drawingNames,
            //);
        //$this->logout();
        return array(
                'drawings' => $this->getDrawingTable()->fetchAll(),
                'drawingCount' => $this->getDrawingTable()->getDrawingCount(),
                'dayCount' => $this->getDrawingTable()->getConsecutiveDaysCount(),
                'name' => $this->sessionContainer->offsetGet("Name"),
            );
     }


     public function viewAction()
     {
        //Get the Id from The URL if there is one, otherwise go to gallery
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {

            //modal views use ajax calls using POST to get image
            $request = $this->getRequest();

            if ($request->isPost()) {
                
                $postData = $request->getPost();
                $id = $postData->id;
                $drawing = $this->getDrawingTable()->getAlbum($id);
                echo '<img class ="mainDrawing" src="/img/upload/'.$drawing->fileName.'" 
                 alt="'.$drawing->title.'">';
                return $this->response;
            }
            return $this->redirect()->toRoute('album', array(
                'action' => 'gallery'
            ));
        }


        //try to grab the info for the picture id added
        try {
            $drawing = $this->getDrawingTable()->getAlbum($id);
            return array( 'drawing' => $drawing,
                'name' => $this->sessionContainer->offsetGet("Name"),
                );
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'gallery'
            ));
        }
     }
     public function dashboardAction()
     {
        if (!$this->userLoggedIn()){
            return $this->redirect()->toRoute('album',array('action'=>'login'));
        }

        

        $lines = file('./html/img/quotes_html.txt');
        $quote =  $lines[array_rand($lines)] ; 
        return array(
            'quote'=>$quote,
            'theme'=>$this->getTheme(),
            'name' => $this->sessionContainer->offsetGet("Name"),
            );
     }

     public function getTheme()
     {
            $date = date('F jS');
            // Get the raw HTML from Reddit.com using cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://www.reddit.com/r/SketchDaily/");
            curl_setopt($ch, CURLOPT_USERAGENT,
              "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $ret = curl_exec($ch);
            curl_close($ch);
            if ($ret === false) {
              echo "Could not fetch Reddit.com page";
              exit;
            }
            $page = $ret;

              if (preg_match("/>$date - (.*?)<\/a>/", $page, $matches))
                 {
                    return $matches[1];
                   
            } else {
              return "Failed Find Today's Theme";
            }
     }

     public function sendEmail()
     {
        $message = new Message();
        $message->addTo('ben@benwann.net')
            ->addFrom('dailysketch@benwann.net', 'Ben at Daily Sketch')
            ->setSubject('Daily Sketch Updates!')
            ->addBcc("");
            
        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'host'              => 'mail.benwann.net',
            'connection_class'  => 'login',
            'connection_config' => array(
                'ssl'       => 'tls',
                'username' => 'dailysketch@benwann.net',
                'password' => ''
            ),
            'port' => 587,
        ));
         
        $html = new MimePart("<p>Hi user!  Email content goes here! woops! </p>");
        $html->type = "text/html";
         
        $body = new MimeMessage();
        $body->addPart($html);
         
        $message->setBody($body);
         
        $transport->setOptions($options);
        $transport->send($message);
     }


     public function weatherAction()
     {
        $zipcode = (int) $this->params()->fromRoute('id', 60614);
        return array('zipcode' =>$zipcode);
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

         public function getTable()
         {
            $table = "User";
             if (!$this->userTable) {
                 $sm = $this->getServiceLocator();
                 $this->userTable = $sm->get('Album\Model\UserTable');
             }
             return $this->userTable;
         }


         public function checkLogin($request){
            $user = $this->getTable()->getUser($request->getPost('username',null));

            $bcrypt = new Bcrypt();

            if ($bcrypt->verify($request->getPost('password',null), $user->password)){

                $this->sessionContainer->offsetSet('Name',$user->username);

                return $this->changeUserStatus();
                }
        }
         public function userLoggedIn(){
            
            return $this->sessionContainer->offsetExists('authenticated');
         }

         public function changeUserStatus(){
            $this->sessionContainer->offsetSet('authenticated',true);
            return $this->redirect()->toRoute('album',array('action'=>'upload-form'));
        }

         public function logoutAction(){
             $this->sessionContainer->offsetUnset('authenticated');
             $this->sessionContainer->offsetUnset('Name');
             $this->sessionContainer->offsetUnset('UserId');

             //return $this->redirect()->toRoute('album',array('action'=>'upload-form'));
         }

         
 }