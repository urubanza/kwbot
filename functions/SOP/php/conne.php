<?php 
$ROOT = [
    "SERVER"=>"172.16.20.232",
    "FOLDER"=>"kwbot",
    "USER_NAME"=>"root",
    "PASSWORD"=>"",
    "HOST"=>"localhost",
    "DATABASE"=>"kwbot",
    "ERROR"=>"",
    "MAIN_PROTACAL"=>"http"
]; 

$ROOT["CONNECTION"] = new mysqli($ROOT["HOST"],$ROOT["USER_NAME"],$ROOT["PASSWORD"]);
$ROOT["LOCATION"] = $ROOT["MAIN_PROTACAL"]."://".$ROOT["SERVER"]."/".$ROOT["FOLDER"]."/";
$ROOT["IMG"] = $ROOT["LOCATION"]."img/";


if($ROOT["CONNECTION"]->connect_error){
   $ROOT["ERROR"] = $ROOT["CONNECTION"]->connect_error;
   echo "Connection failed: ".$ROOT["ERROR"];
} else {
    $ROOT["CONNECTION"]->select_db($ROOT["DATABASE"]);
    if(!mysqli_select_db($ROOT["CONNECTION"],$ROOT["DATABASE"])){
        $ROOT["ERROR"] = mysqli_error($ROOT["CONNECTION"]);
        echo " Error selection of a database : ".$ROOT["ERROR"];
    } 
}

function rootF($index,$EXACT = ""){
    $THE_ROOT = $GLOBALS["ROOT"];
    if(isset($THE_ROOT["$index"])){
        print_r($THE_ROOT["$index"].$EXACT);
    } else {
        print_r("Unkown index (".$index.") !");
    }
}

function imageg($name,$print=true){
    $THE_ROOT = $GLOBALS["ROOT"];
    if($print) print_r(imageg($name,false));
    else {
        if(is_file("../../../img/".$name)){
            return $THE_ROOT["IMG"].$name;
        } else {
            return $THE_ROOT["IMG"]."sys_files/noimage.png";
        }
            
        
    }
}


function sys_img($name){
    if(is_dir("../../../img")){
        if(is_dir("../../../img/sys_files")){
            if(is_file("../../../img/sys_files/".$name)){
                return imageg("sys_files/".$name,false);
            } else return "System error: the file ($name) was not found in the sys_files directory";
        } else {
            mkdir("../../../img/sys_files/");
            return "System error: the sys_files directory was removed in the image directory";
        }
    } else {
        mkdir("../../../img/sys_files/");
        return "System error: the img directory was removed on the server"; 
    } 
}

function imageavatar($name,$print = true){
    $THE_ROOT = $GLOBALS["ROOT"];
    if($print) print_r(imageavatar($name,false));
    else {
        return $THE_ROOT["IMG"]."sys_files/avatar/".strtolower(substr($name,0,1)).".png";
    }
}

function config_js($locs,$lets = false){
    if(!$lets)
        echo '<script type="text/javascript" src="'.$locs.'functions/SOP/js/config.js"></script>';
    else return '<script type="text/javascript" src="'.$locs.'functions/SOP/js/config.js"></script>';
}

function functions_js($locs,$lets = false){
    if(!$lets)
        echo '<script type="text/javascript" src="'.$locs.'functions/js/functions.js"></script>';
    else return '<script type="text/javascript" src="'.$locs.'functions/js/functions.js"></script>';
}
?>
