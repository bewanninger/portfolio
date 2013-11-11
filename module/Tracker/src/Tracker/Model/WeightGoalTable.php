<?php
namespace Tracker\Model;

 use Zend\Db\TableGateway\TableGateway;

 class WeightGoalTable
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

     public function getWeightGoal($userId)
     {
         $id  = (int) $userId;
         $rowset = $this->tableGateway->select(array('UserId' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         // return just the first result
         return $row;
     }

     public function getHistory($userId)
     {
         $id  = (int) $userid;
         $rowset = $this->tableGateway->select(array('UserId' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         //send back all of the weightGoals for the user
         return $rowset;
     }

     public function saveWeightGoal(WeightGoal $newWeightGoal)
     {
         $data = array(
             'UserId'  => $newWeightGoal->id,
             'CurrentWeight' => $newWeightGoal->currentWeight,
             'GoalWeight' => $newWeightGoal->goalWeight,
             'GoalDate' => $newWeightGoal->goalDate,
         );
             $this->tableGateway->insert($data);
         
     }

     public function deleteWeightGoal($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }