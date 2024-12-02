<?php
   $robots_type->create();
   $vehicles->create();
   $vehicles_type->create();
   $vehicles_logs->create();
   $path->create();
    
   $obstacles->create();
   $obstacles_map->create();
   $ultra->create();

    // inserting some data with ultra mapping

    if($ultra->_gets_()->height()!=8){
        $ultra->empty();
        $data_to = pipArr([
            [
                "name"=>"FR"
            ],
            [
                "name"=>"FL"  
            ],
            [
                "name"=>"BR"
            ],
            [
                "name"=>"BL"
            ],
            [
                "name"=>"RF"
            ],
            [
                "name"=>"RB"
            ],
            [
                "name"=>"LF"
            ],
            [
                "name"=>"LB"
            ]
        ]);
        
        if(!$ultra->_add_($data_to)){
            throw new Exception('could not add obstacle sensors list to the database some obstacle will not work!');
        }
    }
?>