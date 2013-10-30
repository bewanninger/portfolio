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

     public function getMood($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('UserId' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function getMoodHistory($id)
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
         echo var_dump($newFood);
         $data = array(
             'UserId' => ((!empty($newFood->userId)) ? $newFood->userId : 999),
             'Quantity' => $newFood->quantity,
             'FoodId' => ((!empty($newFood->foodId)) ? $newFood->foodId : 999),
             'Date' => Date('Y-m-d'),
         );
         echo var_dump($data);
         //die;
         //$id = (int) $newFood->foodId;
         $this->tableGateway->insert($data);
     }

     public function deleteAlbum($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }