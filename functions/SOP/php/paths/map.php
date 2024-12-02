<?php    
    $locs = "../../../../";
    include($locs."modules/header.php");
    include("controller.php");
    $temp = new template("unkown");
    $auth = new auth("login","loginme");

    $all_paths = new paths();

    if(http("data_ids")->set()){
        $the_path = $all_paths->get(http("data_ids")->val());
        if($the_path->height()==1){
            $temp
                ->body("_paths/map.html")
                ->var("path_name",$the_path->JS()->path_name)
                ->var("path_codes",js()->var("path_codes","new path().load('".$the_path->JS()->coordinates."')"))
                ->renderAjax()
                ->echo();
        }
            
    }
    

    
    
?>