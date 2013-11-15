<?php
namespace Tracker\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Where;
 use Zend\Db\Sql\Select;
 use Zend\Db\Sql\Predicate\Predicate;

 class FoodItemTable
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

     public function foodList($query)
     {
        /*
        echo $query;
        
        $rowSet = $this->tableGateway->select(function (Select $select) {
            $select->where->like('Name', "'%D%'");
        });
        */
/*
        $filter =  new Predicate(); 
        $filter->like('Name','%a%'); 
        $rowSet = $this->tableGateway->select(function(Select $select) use 
        ($filter){ 
        $select->where($filter);
        }); 
*/
        //echo $query;
        $rowSet = $this->tableGateway->select(function (Select $select) use ($query) {
                $select->where("Name LIKE '%".$query."%'");
        });


        $foodList = array();
        foreach ($rowSet as $foodItem)
        {
            array_push($foodList, $foodItem->name);
        }

        
        return $foodList;
        
     }

     public function getFoodItem($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('FoodId' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function getFoodId(FoodItem $foodItem)
     {
        #todo
        //echo var_dump($foodItem);
        $foodName = $foodItem->name;
        $rowset = $this->tableGateway->select(array('Name' => $foodName));
        $row = $rowset->current();
        //echo "This is row!:";
        //echo var_dump($row->foodId);
        if(!$row) {
            return false;
        }
        $id = $row->foodId;
        //$id = 1;
        return $id;
     }


     public function getFoodHistory($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('UserId' => $id));
         if (!$rowset) {
             throw new \Exception("Could not find any foods for $id");
         }
         return $rowset;
     }

     public function saveFoodItem(FoodItem $newFood)
     {
         //echo var_dump($newFood);
         //die;
         $data = array(
             'Name' => $newFood->name,
             'Calories'  => ((!empty($newFood->calories)) ? $newFood->calories : 0),
             'Carbs' => ((!empty($newFood->carbs)) ? $newFood->carbs : 0),
             'Fat' => ((!empty($newFood->fat)) ? $newFood->fat : 0),
             'Protein' => ((!empty($newFood->protein)) ? $newFood->protein : 0),
             'Alcohol' => ((!empty($newFood->alcohol)) ? $newFood->alcohol : 0),
         );
         //echo "Not saving to fooditem table becuase of comments";
        $this->tableGateway->insert($data);
         
     }

     public function deleteFood($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }

     

 }