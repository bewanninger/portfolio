<?php
namespace Album\Model;

 use Zend\Db\TableGateway\TableGateway;

 class DrawingTable
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

     public function getAlbum($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('DrawingId' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveAlbum(Drawing $album)
     {
         $data = array(
             'FileName' => $album->fileName,
             'title'  => $album->title,
             //'user'  => $album->user,
         );

         $id = (int) $album->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getAlbum($id)) {
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

    public function getDrawingCount()
    {
        $resultSet = $this->tableGateway->select();

         return count($resultSet);
    }

    public function getConsecutiveDaysCount()
    {

        $resultSet = $this->tableGateway->select();
        $count = 0; #initialize the day counter
        $timeStamps = array(); 
        $today = date('U');
        $oneDay = 60*60*24;

        #put timestamps into array for sorting and comparison 
        foreach($resultSet as $row) 
        {
            array_push($timeStamps,date('Y-m-d',strtotime($row->timeStamp)));
        }

        arsort($timeStamps);
        
        foreach ($timeStamps as $timeStamp)
        {
            #See if the timestamps are less than a day apart - Uses today as starting point
            # increases acceptable gap by one day each time
            if (($timeStamp == date('Y-m-d', ($today-($oneDay*$count))))
                || ($timeStamp == date('Y-m-d', ($today-($oneDay))))) {
                $count++;
            }
            else { break; }
            
        }
        return $count;
    }

 }