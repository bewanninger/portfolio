<?php
namespace Tracker\Model;

 use Zend\Db\TableGateway\TableGateway;

 class FoodLogTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getFoodLogHistory($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('UserId' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $rowset;
     }

     public function saveFoodLog(FoodLog $newFood)
     {
         //echo var_dump($newFood);
         $data = array(
             'UserId' => ((!empty($newFood->userId)) ? $newFood->userId : 999),
             'Quantity' => $newFood->quantity,
             'FoodId' => ((!empty($newFood->foodId)) ? $newFood->foodId : 999),
             'Date' => Date('Y-m-d'),
         );
         //echo var_dump($data);
         //die;
         //$id = (int) $newFood->foodId;
         //echo "Yo, it did not save because it is commented out";
         $this->tableGateway->insert($data);
     }

     public function deleteFoodLog($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }

     public function getTodayLog($userId)
     {
        $select = $this->tableGateway->getSql()->select()
        ->join('FoodItem', 'FoodLog.FoodId=FoodItem.FoodId',
            array('Name'=>'Name',
                 'Calories'=>'Calories',
                 'Fat'=>'Fat',
                 'Carbs'=>'Carbs',
                 'Protein'=>'Protein',
                 'Alcohol'=>'Alcohol',
                 ))
        ->where(array('Date' => Date('Y-m-d'),'UserId' => $userId));

        $resultSet = $this->tableGateway->selectWith($select);
       
        return $resultSet;
     }

     public function getDailyStats($userId)
     {
        $select = $this->tableGateway->getSql()->select()
        ->join('FoodItem', 'FoodLog.FoodId=FoodItem.FoodId',
            array('Name'=>'Name',
                 'Calories'=>'Calories',
                 'Fat'=>'Fat',
                 'Carbs'=>'Carbs',
                 'Protein'=>'Protein',
                 'Alcohol'=>'Alcohol',
                 ))
        ->where(array('Date' => Date('Y-m-d'),'UserId'=> $userId));

        $resultSet = $this->tableGateway->selectWith($select);
        $dataArray = iterator_to_array($resultSet,true);
        
        $dailyStatSum = $this->dailyTotal($dataArray);
        return $dailyStatSum;
     }

     public function getWeeklyStats($weekDates,$userId)
     {

        $weeklyData = array();
        $select = $this->tableGateway->getSql()->select()
        ->join('FoodItem', 'FoodLog.FoodId=FoodItem.FoodId',
            array('Name'=>'Name',
                 'Calories'=>'Calories',
                 'Fat'=>'Fat',
                 'Carbs'=>'Carbs',
                 'Protein'=>'Protein',
                 'Alcohol'=>'Alcohol',
                 ))
        ->where(array('Date' => $weekDates,'UserId' => $userId));
        $resultSet = $this->tableGateway->selectWith($select);

        $dataArray = iterator_to_array($resultSet,true);

        
        
        foreach ($weekDates as $date)
        {
            $dailyItems = array();
            foreach ($dataArray as $foodItem)
            {
                if ($date == $foodItem->date)
                {
                    array_push($dailyItems,$foodItem);
                }
            }
            $dailyStatSum = $this->dailyTotal($dailyItems);
            array_push($weeklyData,$dailyStatSum);
        }
        return $weeklyData;
     }

     public function dailyTotal($dataArray)
     {
        $dailyTotals = array("calories" => 0,
                            "fat" => 0,
                            "carbs" => 0,
                            "protein" => 0,
                            "alcohol" => 0);
        $categoryToSum = array("calories","fat","carbs","protein","alcohol");
        foreach($dataArray as $foodItem)
        {

            foreach($categoryToSum as $category)
            {
            $dailyTotals[$category] += ($foodItem->$category * $foodItem->quantity);
            }
        }
            return $dailyTotals;
     }

     public function dailyPercentages($dataArray)
     {
        #Todo - Return category breakdown of percentage of daily Calories
     }

}