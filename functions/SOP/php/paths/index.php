<?php    
    $locs = "../../../../";
    include($locs."modules/header.php");
    include("controller.php");
    $temp = new template("unkown");
    $auth = new auth("login","loginme");

    $all_paths = new paths();

    $temp
        ->body("_paths/main.html")
        ->var("LAST_PATHS",$all_paths->last())
        ->renderAjax()
        ->echo();
    
?>
