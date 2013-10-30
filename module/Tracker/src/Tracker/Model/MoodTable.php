<?php
namespace Tracker\Model;

 use Zend\Db\TableGateway\TableGateway;

 class MoodTable
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

     public function saveMood(Mood $newMood)
     {
         //echo var_dump($newMood);
         //die;
         $data = array(
             'Mood' => $newMood->mood,
             'UserId'  => "1",
             //'user'  => $album->user,
         );

         $id = (int) $newMood->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getMood($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Album id does not exist');
             }
         }
     }

     public function deleteAlbum($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }