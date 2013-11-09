<?php
namespace Tracker\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;
use Tracker\Form\MoodForm;
use Tracker\Form\FoodItemForm;
use Tracker\Form\FoodLogForm;
use Tracker\Form\LoginForm;
use Tracker\Form\WeightGoalForm;
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
    protected $userTable;
    protected $weightGoalTable;


	public function __construct()
    {
        $this->sessionContainer = new Container('tracker');
        //$this->sessionContainer->offsetSet("Name","Guest");
        //$this->sessionContainer->offsetSet("UserId",999);
        $this->sessionContainer->offsetSet("Epoch",strtotime("2013-09-29"));
        $this->sessionContainer->offsetSet("Name",
            ($this->sessionContainer->offsetExists('Name')) ? 
            $this->sessionContainer->offsetGet("Name") : "Guest");
        $this->sessionContainer->offsetSet("UserId",
            ($this->sessionContainer->offsetExists('UserId')) ? 
            $this->sessionContainer->offsetGet("UserId") : 999);
        //$this->foodId     = (!empty($data['FoodId'])) ? $data['FoodId'] : null;
    }

	public function indexAction(){
		$moodForm = new MoodForm();
        $foodItemForm = new FoodItemForm();
        $foodLogForm = new FoodLogForm();

        $userId = $this->sessionContainer->offsetGet('UserId');

        
		return array('moodForm' => $moodForm,
            'foodForm' => $foodItemForm,
            'name' => $this->sessionContainer->offsetGet("Name"),
            'today' => $this->getFoodLogTable()->getTodayLog($userId),
            'dailyStats' => $this->getFoodLogTable()->getDailyStats($userId),
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

    public function addMoodAction()
        {
        $moodForm = new MoodForm();
        $request = $this->getRequest();


        if ($request->isPost()) {
            $newMood = new Mood();
            $moodForm->setInputFilter($newMood->getInputFilter());
            $moodForm->setData($request->getPost());

            if ($moodForm->isValid()){
                $newMood->exchangeArray($moodForm->getData());
                $newMood->id = $this->sessionContainer->offsetGet('UserId');
                $this->getMoodTable()->saveMood($newMood);

                echo json_encode($newMood);
                return $this->response;
                //return array("moodForm" =>$moodForm);
            }
            
        }
        
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

                if ($form->isValid()) {
                    $foodItem->exchangeArray($form->getData());
                    $foodId = $this->getFoodItemTable()->getFoodId($foodItem);

                    if($foodId){
                        $postQuantity = $request->getPost('Quantity');
                        $this->addItemToFoodLog($foodId,$postQuantity);
                        echo "Food Was Found and Added";
                        echo json_encode($request->getPost());
                        return $this->response;
                    } else {
                        $this->getFoodItemTable()->saveFoodItem($foodItem);
                        $foodId = $this->getFoodItemTable()->getFoodId($foodItem);

                        $postQuantity = $request->getPost('Quantity');
                        $this->addItemToFoodLog($foodId,$postQuantity);
                        echo "Food Item was created and then added to log";
                        echo json_encode($request->getPost());
                        return $this->response;
                        //return "hello";
                    }
                }
                //if the form is not valid
                else {
                    echo "Form was not valid";
                    echo json_encode($request->getPost());
                    return $this->response;
                }
            } 
        } else { 
            #Use the URI to add an already existing food Item to the foodLog
                  echo $this->addItemToFoodLog($id,$quantity);
                  return $this->response;
                  //echo "retry";
        }
        echo "No Post - No ID";
        return $this->response;
        //return $this->redirect()->toRoute('tracker');
     }

     public function foodinfoAction(){
        $foodItem = new FoodItem();
        $form = new FoodItemForm();

            $request = $this->getRequest();
            if ($request->isPost()) {

                $form->setInputFilter($foodItem->getInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $foodItem->exchangeArray($form->getData());
                    $foodId = $this->getFoodItemTable()->getFoodId($foodItem);
                    try { 
                        $foodItemInfo = $this->getFoodItemTable()->getFoodItem($foodId);
                        echo json_encode($foodItemInfo);
                    } catch (\Exception $e){
                        echo "none found";
                    }  
                } 
                else {
                    echo "none found";
                }
                return $this->response;
            }
            else{
                echo "Whatchu doin here? Get out of here.";
                return false;
                //return $this->response;
            }

                
     }

     public function dashboardAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        $userId = $this->sessionContainer->offsetGet('UserId');

        $day = 60*60*24;
        $weekDates = array();
        $weekDays = "[";
        for ($i=0;$i<7;$i++){
            $dayTime = time()-($day*$i);
            array_push($weekDates,date('Y-m-d',$dayTime));
            $weekDays .= '"'.date('l', $dayTime).'",'; 
        }
        $weekDays = rtrim($weekDays, ",")."]";
        //$weekDays .= "]";

        $weekStats = $this->getFoodLogTable()->getWeeklyStats($weekDates,$userId);
        $weekChartData = $this->formatChartData($weekStats);

        return array('weekDays'=>$weekDays,
                    'weekData' => $weekChartData);
     }


     public function loginAction()
     {
        $form = new LoginForm();
        //$form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            //check the username and pass
            $this->checkLogin($request);

            return array('request' => $request,
                          'form' => $form,
                          'session' => $this->sessionContainer->offsetGet('user'),);
        }
        return array('form' => $form,
                     'request' => 'nope',
                     'userName' => $this->sessionContainer->offsetGet('Name'),
                     'status' => $this->userLoggedIn(),
                     );
     }


    public function logoutAction(){
             $this->sessionContainer->offsetUnset('authenticated');
             $this->sessionContainer->offsetUnset('Name');
             $this->sessionContainer->offsetUnset('userId');


             //return $this->redirect()->toRoute('album',array('action'=>'upload-form'));
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

     public function formatChartData($weeklyStats)
     {
        $chartData = array("calories" => "[",
                            "fat" => "[",
                            "carbs" => "[",
                            "protein" => "[",
                            "alcohol" => "[");
        $categories = array("calories","fat","carbs","protein","alcohol");
        foreach($weeklyStats as $dailyStat)
        {
            foreach($categories as $cat)
            {
                $chartData[$cat] .= '"'.$dailyStat[$cat].'",';
                
            }
        }
        foreach($categories as $cat)
            {
                $chartData[$cat] = rtrim($chartData[$cat], ",")."]";
                
            }
                return $chartData;
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

    public function getTable()
         {
            $table = "User";
             if (!$this->userTable) {
                 $sm = $this->getServiceLocator();
                 $this->userTable = $sm->get('Tracker\Model\UserTable');
             }
             return $this->userTable;
         }


         public function checkLogin($request){
            #$credentials = $this->sessionContainer->offsetGet('user');
            $user = $this->getTable()->getUser($request->getPost('username',null));

            $bcrypt = new Bcrypt();


            if ($bcrypt->verify($request->getPost('password',null), $user->password)){
                $this->sessionContainer->offsetSet('UserId', $user->id);
                $this->sessionContainer->offsetSet('Name', $user->username);
                return $this->changeUserStatus();
            }
        }

         public function userLoggedIn(){
            
            return $this->sessionContainer->offsetExists('authenticated');
         }

         public function changeUserStatus(){
            $this->sessionContainer->offsetSet('authenticated',true);
            return $this->redirect()->toRoute('tracker');
        }
}