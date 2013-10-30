<?php
namespace Tracker\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;
use Tracker\Form\MoodForm;
use Tracker\Form\FoodItemForm;
use Tracker\Form\FoodLogForm;
use Tracker\Model\Mood;
use Tracker\Model\MoodTable;
use Tracker\Model\FoodItem;
use Tracker\Model\FoodItemTable;
use Tracker\Model\FoodLog;
use Tracker\Model\FoodLogTable;



class TrackerController extends AbstractActionController
{
	protected $sessionContainer;
	protected $moodTable;
    protected $foodLogTable;
    protected $foodItemTable;


	public function __construct()
    {
        $this->sessionContainer = new Container('session');
        $this->sessionContainer->offsetSet("Name","Kate");
        $this->sessionContainer->offsetSet("UserId",2);
    }

	public function indexAction(){
		$moodForm = new MoodForm();
        $foodItemForm = new FoodItemForm();
        $foodLogForm = new FoodLogForm();

        $moods = $this->getMoodTable()->getMood(1);

        $request = $this->getRequest();


        if ($request->isPost()) {
            echo var_dump($request);
        die;
            $newMood = new Mood();
            $moodForm->setInputFilter($newMood->getInputFilter());
            $moodForm->setData($request->getPost());

            if ($moodForm->isValid()){
                $newMood->exchangeArray($moodForm->getData());
                $this->getMoodTable()->saveMood($newMood);
            }
            // Redirect to list of albums
            return array('moodForm' => $moodForm,
                'foodForm' => $foodLogForm,
                'name' => $this->sessionContainer->offsetGet("Name"),
             );
        }
		return array('moodForm' => $moodForm,
            'foodForm' => $foodLogForm,
            'name' => $this->sessionContainer->offsetGet("Name"),
			 );
	}
    
    public function getMoodTable()
         {
            $table = "Mood";
             if (!$this->moodTable) {
                 $sm = $this->getServiceLocator();
                 $this->moodTable = $sm->get('Tracker\Model\MoodTable');
             }
             return $this->moodTable;
         }


         public function addAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        $form = new FoodLogForm();
            //$request = $this->getRequest();
            //echo var_dump($request);

        if(!$id){
            $request = $this->getRequest();
            //echo var_dump($request);
            if ($request->isPost()) {
                $foodItem = new FoodLog();
                $form->setInputFilter($foodItem->getInputFilter());
                $form->setData($request->getPost());
                echo "yup, post";
                if ($form->isValid()) {
                    $foodItem->exchangeArray($form->getData());
                    echo "yup, valid";

                    $this->getFoodLogTable()->saveFoodLog($foodItem);
                    // Redirect to list of albums
                    //return $this->redirect()->toRoute('tracker');
                }
            } 
        } else {
            $foodItem = new FoodLog();
            $foodItem->foodId = $id;
            $foodItem->quantity = 1;
            $foodItem->userId = $this->sessionContainer->offsetGet("UserId");
            //$foodItem = $this->getFoodItemTable->getFoodItem();
            $this->getFoodLogTable()->saveFoodLog($foodItem);            
        }

        

        
        //return $this->redirect()->toRoute('tracker');
     }

     public function getFoodLogTable()
     {
        $table = "FoodLog";
         if (!$this->foodLogTable) {
             $sm = $this->getServiceLocator();
             $this->foodLogTable = $sm->get('Tracker\Model\FoodLogTable');
         }
         return $this->foodLogTable;
     }

     public function getFoodItemTable()
     {
        $table = "FoodItem";
         if (!$this->foodItemTable) {
             $sm = $this->getServiceLocator();
             $this->foodItemTable = $sm->get('Tracker\Model\FoodItemTable');
         }
         return $this->foodLogTable;
     }
}