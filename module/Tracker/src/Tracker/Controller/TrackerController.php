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
        $this->sessionContainer->offsetSet("Epoch",strtotime("2013-09-29"));

        #set username and user ID to default if not logged in
        $this->sessionContainer->offsetSet("Name",
            ($this->sessionContainer->offsetExists('Name')) ? 
            $this->sessionContainer->offsetGet("Name") : "Guest");
        $this->sessionContainer->offsetSet("UserId",
            ($this->sessionContainer->offsetExists('UserId')) ? 
            $this->sessionContainer->offsetGet("UserId") : 999);
    }

	public function indexAction(){
        #Initialize Forms Needed For View
		$moodForm = new MoodForm();
        $foodItemForm = new FoodItemForm();
        $foodLogForm = new FoodLogForm();
        #get user id to populate data for user
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
            }
        }
    }

    public function addAction()
     {
        #get URI params and set defaults if none
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

                    if($foodId){#if food was found in database
                        $postQuantity = $request->getPost('Quantity');
                        $this->addItemToFoodLog($foodId,$postQuantity);
                        
                        echo json_encode($request->getPost());
                        return $this->response;
                    } else {#if food item has to be added to database
                        $this->getFoodItemTable()->saveFoodItem($foodItem);
                        $foodId = $this->getFoodItemTable()->getFoodId($foodItem);

                        $postQuantity = $request->getPost('Quantity');
                        $this->addItemToFoodLog($foodId,$postQuantity);
                        
                        echo json_encode($request->getPost());
                        return $this->response;
                    }
                }
                #if the form is not valid
                else {
                    echo "Form was not valid";
                    return $this->response;
                }
            } 
        } else { 
            #Use the URI to add an already existing food Item to the foodLog
                  echo $this->addItemToFoodLog($id,$quantity);
                  return $this->response;
        }
        echo "No Post - No ID";
        return $this->response;
     }

     #Takes a post request with foodItem form
     public function foodinfoAction(){ 
        #initialize objects for view
        $foodItem = new FoodItem();
        $form = new FoodItemForm();

            $request = $this->getRequest();
            if ($request->isPost()) { #check for post

                $form->setInputFilter($foodItem->getInputFilter());
                $form->setData($request->getPost()); #use post data to make form

                if ($form->isValid()) { # make sure data checks out
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
            }

                
     }


     public function dashboardAction()
     {
        $userId = $this->sessionContainer->offsetGet('UserId');

        $weekDates = $this->weekDates(); # get the dates (y-m-d) for the week

        $weekStats = $this->getFoodLogTable()->getWeeklyStats($weekDates,$userId);
        $weekChartData = $this->formatChartData($weekStats);

        return array('weekDays'=> $this->weekDays(),
                    'weekData' => $weekChartData);
     }


     public function loginAction()
     {
        $form = new LoginForm();
        //$form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) { //check the username and pass if one was submitted
            
            try { 
            $this->checkLogin($request);
            }
            catch (\Exception $e){
            }
            
            return array('errorMessageClass' => 'alert alert-danger',
                          'form' => $form,
                          'session' => $this->sessionContainer->offsetGet('user'),);
        }
        return array('form' => $form,
                     'errorMessageClass' => 'hidden',
                     'userName' => $this->sessionContainer->offsetGet('Name'),
                     'status' => $this->userLoggedIn(),
                     );
     }


    public function logoutAction(){
            #unset session authentication and user info
             $this->sessionContainer->offsetUnset('authenticated');
             $this->sessionContainer->offsetUnset('Name');
             $this->sessionContainer->offsetUnset('userId');
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

    #returns string of last 7 days in proper format for chart.js label 
     public function weekDays()
     {
        $day = 60*60*24;
        $weekDays = "[";
        for ($i=0;$i<7;$i++){
            $dayTime = time()-($day*$i);
            $weekDays .= '"'.date('l', $dayTime).'",'; 
        }
        $weekDays = rtrim($weekDays, ",")."]";
        return $weekDays;
     }

     #returns array of last 7 days' dates in (y-m-d) format
     public function weekDates()
     {
        $day = 60*60*24;
        $weekDates = array();
        for ($i=0;$i<7;$i++){
            $dayTime = time()-($day*$i);
            array_push($weekDates,date('Y-m-d',$dayTime));
        }
        return $weekDates;
     }

     #reuturns strings with data formatted to be used for Chart.js charts
     public function formatChartData($weeklyStats)
     {
        #initialize and array with keys for all the datasets
        $chartData = array("calories" => "[",
                            "fat" => "[",
                            "carbs" => "[",
                            "protein" => "[",
                            "alcohol" => "[");
        $categories = array("calories","fat","carbs","protein","alcohol");
        #add each daily stat to each category in proper string format for chart.js
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