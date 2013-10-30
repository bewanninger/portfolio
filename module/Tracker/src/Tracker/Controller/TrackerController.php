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
        $this->sessionContainer->offsetSet("Epoch",
            strtotime("2013-09-29"));
    }

	public function indexAction(){
		$moodForm = new MoodForm();
        $foodItemForm = new FoodItemForm();
        $foodLogForm = new FoodLogForm();

        $request = $this->getRequest();


        if ($request->isPost()) {
            $newMood = new Mood();
            $moodForm->setInputFilter($newMood->getInputFilter());
            $moodForm->setData($request->getPost());

            if ($moodForm->isValid()){
                $newMood->exchangeArray($moodForm->getData());
                $this->getMoodTable()->saveMood($newMood);
            }
            // Redirect to list of albums
            return array('moodForm' => $moodForm,
                'foodForm' => $foodItemForm,
                'name' => $this->sessionContainer->offsetGet("Name"),
             );
        }
		return array('moodForm' => $moodForm,
            'foodForm' => $foodItemForm,
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
        $quantity = (int) $this->params()->fromRoute('quantity',1);

        $form = new FoodItemForm();

        if(!$id){ # check to see if there was a food id passed in the uri,
                    # If not, get the post params and look for the foodItem
                    # redirect if item has ID, if not, add it to FoodItem table. 
            $request = $this->getRequest();
            if ($request->isPost()) {
                $foodItem = new FoodItem();
                $form->setInputFilter($foodItem->getInputFilter());
                $form->setData($request->getPost());
                //echo "yup, post";
                if ($form->isValid()) {
                    $foodItem->exchangeArray($form->getData());
                    //echo "yup, valid";
                    //$this->getFoodLogTable()->saveFoodLog($foodItem);
                    $foodId = $this->getFoodItemTable()->getFoodId($foodItem);

                    if($foodId){
                        // Redirect to list of albums
                        return $this->redirect()->toRoute('tracker',array('action' => 'add',
                        'id' => $foodId,'quantity' => $quantity));
                    } else {
                        $this->getFoodItemTable()->saveFoodItem($foodItem);
                        $foodId = $this->getFoodItemTable()->getFoodId($foodItem);

                        $postQuantity = $request->getPost('Quantity');
                        $this->addItemToFoodLog($foodId,$postQuantity);
                    }
                }
            } 
        } else { 
            #Use the URI to add an already existing food Item to the foodLog
                  $this->addItemToFoodLog($id,$quantity); 
        }
        //return $this->redirect()->toRoute('tracker');
     }


     public function addItemToFoodLog($foodId, $quantity)
     {
        $foodItem = new FoodLog();
            $foodItem->foodId = $foodId;
            $foodItem->quantity = $quantity;
            $foodItem->userId = $this->sessionContainer->offsetGet("UserId");
            $this->getFoodLogTable()->saveFoodLog($foodItem);  
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
         return $this->foodItemTable;
     }
}