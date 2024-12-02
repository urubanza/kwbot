<?php
session_start();
 /* 
 file has been composed by Pacifique Ishimwe a.k.a PIP for commercial and serious issue
    please check lisence before using this. copyright PIP allright reserved.
    ################################################################################################################
  PIP is an application software development libray available for Core PHP/MySql, Core Javascript( DOM for web application) & Core Javascript ( Node for Serverside fullduplexed com. server), x86 & x64 assembly with C/C++ and python, Objective C/Swift( for IOS applications ) ,Java/Kotlin for android applications, CLI/C# .NET for Windows.
  And is for those who need to create great and innovative system and application softwares  designed for IoT, Artificial intergence, blockchain applications and many more advanced engeenering software both application and system softwares.
**/
// a class to keep constants of the root server
class rootConfig {
    public $ROOT = [
        "SERVER"=>"assiasafaris.com",
        "FOLDER"=>"",
        "USER_NAME"=>"root",
        "PASSWORD"=>"",
        "HOST"=>"localhost",
        "ERROR"=>"",
        "MAIN_PROTACAL"=>"http"
    ]; 
    function __construct($folder = ""){
        $this->ROOT["LOCATION"] = "http://".$this->ROOT["SERVER"]."/";
        $this->ROOT["FOLDER"] = $folder;
        if($this->ROOT["FOLDER"]!="")
             $this->ROOT["LOCATION"] = $this->ROOT["LOCATION"].$this->ROOT["FOLDER"]."/";
        $this->ROOT["IMG"] = $this->ROOT["LOCATION"]."img/";
    }
    function conn($database,$user,$password){
        $this->ROOT["CONNECTION"] = new mysqli($this->ROOT["HOST"],$this->ROOT["USER_NAME"],$this->ROOT["PASSWORD"]);
        if($this->ROOT["CONNECTION"]->connect_error){
           $this->ROOT["ERROR"] = $ROOT["CONNECTION"]->connect_error;
           echo "Connection failed: ".$this->ROOT["ERROR"];
        } else {
            $ROOT["CONNECTION"]->select_db($database);
            if(!mysqli_select_db($this->ROOT["CONNECTION"],$database)){
                $this->ROOT["ERROR"] = mysqli_error($this->ROOT["CONNECTION"]);
                echo " Error selection of a database : ".$this->ROOT["ERROR"];
            } 
        }
        return $this->ROOT["CONNECTION"];
    }
    function rootF($index,$EXACT = ""){
        if(isset($this->ROOT["$index"])){
            print_r($this->ROOT["$index"].$EXACT);
        } else {
            print_r("Unkown index (".$index.") !");
        }
    }
    function imageg($name){
        print_r($this->ROOT["IMG"].$name);
    }
}
// a siimplified function to return the rootConfig object
function root($folder){
    return new rootConfig($folder);
}
/* THE CLASS OF ALL APPLICATION ROOT */
class webApp {
 	private $database;
    // the sql query to define the structure of the table or database
    protected $Structure = "";
 	public $message = "no thing wrong yet!";
 	public $err = false;
    public $con;
    
    public $conn;
    public $locs;
    const config = [
                "SERVER"=>"assiasafaris.com",
                "FOLDER"=>"",
                "USER_NAME"=>"root",
                "PASSWORD"=>"",
                "HOST"=>"localhost",
                "ERROR"=>""
    ];
 	function __construct($argument,$host = "",$password = "",$username = "",$struct = "") {
        if($struct=="")
            $this->Structure = "CREATE DATABASE IF NOT EXISTS `".$argument."`";
        else $this->Structure = $struct;
 		$this->database = $argument;
        if($host==""){
            $this->con = [
                "host"=>webApp::config["HOST"],
                "db"=>$argument,
                "user"=>webApp::config["USER_NAME"],
                "password"=>webApp::config["PASSWORD"],
                "status"=>"waiting"
            ];
            $this->_connection_();
        }
        else {
           $this->con = [
                "host"=>$host,
                "db"=>$argument,
                "user"=>$username,
                "password"=>$password,
                "status"=>"waiting"
            ];
           $this->connection($host,$password,$username);  
        }
        
        $this->locs = webApp::path();
        if(is_dir($this->locs.self::config["FOLDER"])){
            if(is_dir($this->locs.self::config["FOLDER"]."/pip_backup/")){
                if(!is_dir($this->locs.self::config["FOLDER"]."/pip_backup/".$this->database."/"))
                    mkdir($this->locs.self::config["FOLDER"]."/pip_backup/".$this->database."/");
            } else {
                mkdir($this->locs.self::config["FOLDER"]."/pip_backup/");
                mkdir($this->locs.self::config["FOLDER"]."/pip_backup/".$this->database."/");
            }   
        }
        else {
            //echo "the root directory(".self::config["FOLDER"].") does't exist";
        }
        $this->con["backup"] = $this->locs.self::config["FOLDER"]."/pip_backup/".$this->database."/";
 	}
 	public function _connection(){
      return $this->con;
 	}
    public function _connection_(){
        $this->conn = new mysqli($this->con["host"],$this->con["user"],$this->con["password"]);
		// Check connection
		if ($this->conn->connect_error) {
			$message = "!!! Connection failed: " . $this->conn->connect_error;
            
            $this->con["status"] = "failed";
            
		}
		else {
		      $db_selected = mysqli_select_db($this->conn,$this->con["db"]);
		      if($db_selected){
                  $this->con["status"] = "success";
                  $this->message =  "database selected successfully";
              }
              else{
                  $this->message = "database <b>".$this->database."</b> was not selected <b><i>".mysqli_error($this->conn);
                  $this->con["status"] = "failed";
              } 
      }
      return $this->conn;
    }
 	public function connection($host,$password,$username){
 		// Create connection
        $this->con = [
                "host"=>$host,
                "user"=>$username,
                "password"=>$password,
                "status"=>"waiting"
        ];
      return $this->con;
 	}
 	public function getError(){
        return self::message;
 	}
    public function table($names, $id="", $host = "",$password = "",$username = ""){
        $primary_key = $id;
        if($id==""){
            $primary_key = $names."_id";
        }
        $table = new admin($names,$primary_key);
        if(!($host=="")){
           $table =  new admin($names,$primary_key,$this->conn);  
        }
        $table = $table->con($this->con);
        $table->close();
        return $table;
    }
    public function client($name){
        return new PIPCLENTS($name,$this->con);
    }
    public function client_list($LIST){
        return CLIENT_LIST($LIST,$this->con);
    }
    public function whos_log($LIST){
        for($cc=0;$cc<sizeof($LIST);$cc++){
            
        }
    }
    //a public functin to close the mysql connection
    public function close(){
        if($this->conn instanceof mysqli){
            $this->conn->close();
            $this->conn = 0;
        } else {
           $this->conn = 0; 
        }
    }
    //a public functin to open the mysql connection
    public function open(){
        $this->conn = $this->_connection_();
        return $this->conn;
    }
    // a function to create the table/database
    public function create(){
        $this->open();
        if(mysqli_query($this->conn,$this->Structure)){
            $this->message = "Object initiation success (:";
            $this->close();
            return true;
        } else {
            $this->message = "Object initiation failed ): ".mysqli_error($this->conn);
            $this->close();
            return false;
        }
    }
    // a public function to define structure of table/ database 
    public function initial($struct){
        $this->Structure = $struct;
        return $this;
    }
    // a function to return the path of the calling file in form string
    public static function path(){
        $locs = $_SERVER["PHP_SELF"];
        $path = array();
        for($ii=0;$ii<strlen($locs);$ii++){
               if(substr($locs,$ii,1)=="/"){
                   array_push($path,"");
               } else {
                   if(sizeof($path))
                       $path[sizeof($path)-1] .= substr($locs,$ii,1);
               } 
        }
        
        $locs = "";
        for($ii=0;$ii<sizeof($path);$ii++)
            $locs = "../";
        
        return $locs;
    }
}
function database($argument,$host = "",$password = "",$username = "", $struct = ""){
    return new webApp($argument,$host,$password,$username);
}
/* THE CLASS OF ALL ACTIVITIES DONE BY CLIENT LIKE VISIT, VIEW, REACH, LOGIN, LOGOUT */
class PIPCLENTS extends webApp {
    // a local variable that will keep user location in json format
    public $user_location = "";
    // local variable that will store a session name
 	public $user_session_name = "";
    // local variable that will store the session id
 	public $user_session_id = 0;
    // local variable that will store if a session  is present or not 
 	public $logged_in = false;
    public $user_session_present = 0;
    // local variable that will store the type of the client on the web
 	public $user_session_type = "";
    // those are public objects to store all of visitors tables and user account respectively
    public $visits;
    public $useraccounts;
    // local variable that will store the error message if it is occured
 	public $message = "you are not logged in";
    // public variable to keep connecion info to the database
    public $conn;
    // a public variable to credentials of database connection
    public $con;
    // list of not allowed characters for username 
    protected $not_allowed_chars = [];
    public $status = true;
    // list of compilisory characters for passwords
    protected $compulsery_chars = [];
        // definition of errors and associated boolean memory
        // definition of error type integer where :
        #0 is no error ,
        #1 is a  success  registration,
        #2 is a failed registration,
        #3 is a success login ,
        #4 is a failed login with inactivated account,
        #5 failed login with wrong password,
        #6 failed login with unkown email,
        #7 failed login with internal System error 
        #8 password reset request with existing email and reset link created(sent) successfull
        #9 password reset request with unkown email address
        #10 password reset request with existing email and reset link failed
        #11 registration success with activation link failed
        #12 registration success with activation link success with negative feedback
        #13 registration success with activation link success with positive feedback
        #14 password reset request with positive feedback
        #15 password reset request with negative feedback
        #16 Unkown system error
        #17 failed login with wrong password, and Unactivated account
        #18 invalid input, Provided informations from the user are not valid
        #19 user logged out 
    public $errorsLOGSMessages = ["Login First",
                                  "registration success with activation link success",
                                  "registration failed",
                                  "login success",
                                  "login failed with inactivated account",
                                  "login failed with wrong password",
                                  "login failed with unkown email",
                                  "login failed with internal System error",
                                  "password reset request with existing email and reset link created(sent) successfull",
                                  "password reset request with unkown email address",
                                  "password reset request with existing email and reset link failed",
                                  "registration success with activation link failed",
                                  "registration success with activation link success with negative feedback",
                                  "registration success with activation link success with positive feedback",
                                  "password reset request with positive feedback",
                                  "password reset request with negative feedback",
                                  "Unkown system error",
                                  "login failed with wrong password and Unactivated account",
                                  "invalid input, Provided informations from the user are not valid",
                                  "Logging out success"];
    // array to store Steps of dangerous errors where 0 there is no problem , 1 medium error and 2 dangerous error
    public $errorsLOGSMessagesDanger = [0,
                                 0,
                                 2,
                                 0,
                                 1,
                                 1,
                                 2,
                                 2,
                                 0,
                                 2,
                                 1,
                                 1,
                                 2,
                                 0,
                                 0,
                                 2,
                                 2,
                                 1];
    // the constructor of our client variable 
    // the first parameter '$argument' is the name of those clients and $con is the connection variable to the database
 	function __construct($argument,$con){
 		$this->session_name = $argument;
        if($con instanceof mysqli){
            $con->close();
            unset($con);
            $this->conn = 0;
        } else if(isset($con["host"])&&isset($con["db"])&&isset($con["user"])&&isset($con["password"])){
            $this->con($con);
        }
        
        $this->conn = $this->_connection_();
        // creation of table to store visit tracking 
        // time to save visited time, location to store location in json format, screen to store user screen resolution in WxH format,
        //session_id to store use unique session of a user in 50 number format where 
        $this->sql = "CREATE TABLE IF NOT EXISTS `$argument` (`".$argument."_id` int(11) NOT NULL ,`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,`location` VARCHAR(200) NOT NULL,`screen` VARCHAR(200) NOT NULL,`session_id` INT(11) NOT NULL,`page` VARCHAR(50),`logged_in` tinyint(1) NOT NULL NOT NULL,`".$argument."_acounts` INT(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET = latin1";
        if(mysqli_query($this->conn,$this->sql)){
           $this->message = "$argument created well";
           $this->sql = "ALTER TABLE `".$argument."` ADD PRIMARY KEY (`".$argument."_id`)";
              if(mysqli_query($this->conn,$this->sql)){
                      $this->message = "$argument this has been done well";
                      $this->sql = "ALTER TABLE `$argument` MODIFY `".$argument."_id` int(11) NOT NULL AUTO_INCREMENT";
                      if(mysqli_query($this->conn,$this->sql)){
                          $this->message = "$argument created well";
                       }  else $this->message = $argument.' not done there was some errors  '.mysqli_error($this->conn);
                  }  else $this->message = $argument.' not done there was some errors  :'.mysqli_error($this->conn);
        } 
        else $this->message = $argument.' Not Created : '.mysqli_error($this->conn);
        // creation of table to store registered users where activated will save the current state of a user account : 0 is not activated,1 is activated , 2 is blocked, 3 is suspended
        $this->sql = "CREATE TABLE IF NOT EXISTS `".$argument."_acounts` (`".$argument."_acounts_id` int(11) NOT NULL,`time_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,`username` VARCHAR(200) NOT NULL,`email` VARCHAR(200) NOT NULL,`password` VARCHAR(200) NOT NULL,`profile_pic` VARCHAR(200) NOT NULL,`cover_pic` VARCHAR(200) NOT NULL,`activated` INT(11) NOT NULL, `tel` VARCHAR(100) NOT NULL, `fname` VARCHAR(100) NOT NULL, `lname` VARCHAR(100) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET = latin1";
        if(mysqli_query($this->conn,$this->sql)){
           $this->message = "$argument"."_acounts created well";
           $this->sql = "ALTER TABLE `".$argument."_acounts` ADD PRIMARY KEY (`".$argument."_acounts_id`)";
              if(mysqli_query($this->conn,$this->sql)){
                      $this->message = "$argument this has been done well";
                      $this->sql = "ALTER TABLE `".$argument."_acounts`  MODIFY `".$argument."_acounts_id` int(11) NOT NULL AUTO_INCREMENT";
                      if(mysqli_query($this->conn,$this->sql)){
                          $this->message = "$argument"."_acounts"."created well";
                       }  else $this->message = $argument.'_acounts not done there was some errors  '.mysqli_error($this->conn);
                  }  else $this->message = $argument.' not done there was some errors  :'.mysqli_error($this->conn);
        } else $this->message = $argument.' accounts Not Created : '.mysqli_error($this->conn);
        // creation of the table to save users logs within the system where logs_time will save the action happened logs_type type of log happened and logs_user will keep the user id of the logs
        
        $this->sql = "CREATE TABLE IF NOT EXISTS `".$argument."_logs` (
                          `".$argument."_logs_id` int(11) NOT NULL,
                          `".$argument."_logs_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                          `".$argument."_logs_type` int(11) NOT NULL,
                          `".$argument."_logs_user` int(11) NOT NULL
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
        if(mysqli_query($this->conn,$this->sql)){
            $this->message = "`$argument"."_logs` created well";
            $this->sql = "ALTER TABLE `".$argument."_logs` ADD PRIMARY KEY (`".$argument."_logs_id`)";
            if(mysqli_query($this->conn,$this->sql)){
                $this->message = "$argument this has been done well";
                $this->sql = "ALTER TABLE `".$argument."_logs` MODIFY `".$argument."_logs_id` int(11) NOT NULL AUTO_INCREMENT";
                if(mysqli_query($this->conn,$this->sql)){
                    $this->message = "$argument"."_logs"."created well";
                } else $this->message = $argument.'_logs not done there was some errors  :'.mysqli_error($this->conn);
            }  else $this->message = $argument.' not done there was some errors  :'.mysqli_error($this->conn);
        } else $this->message = $argument.' logs Not Created : '.mysqli_error($this->conn);
        // creation of the table that will store user sessions 
        $this->sql = "CREATE TABLE IF NOT EXISTS `".$argument."_sessions` (
                        `".$argument."_sessions_id` int(11) NOT NULL,
                        `".$argument."_sessions_data` TEXT,
                        `".$argument."_time` TIMESTAMP NOT NULL
                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
        if(mysqli_query($this->conn,$this->sql)){
            $this->message = "`$argument"."_logs` created well";
            $this->sql = "ALTER TABLE `".$argument."_sessions` ADD PRIMARY KEY (`".$argument."_sessions_id`)";
            if(mysqli_query($this->conn,$this->sql)){
                $this->message = "$argument this has been done well";
                $this->sql = "ALTER TABLE `".$argument."_sessions` MODIFY `".$argument."_sessions_id` int(11) NOT NULL AUTO_INCREMENT";
                if(mysqli_query($this->conn,$this->sql)){
                    $this->message = "$argument"."_sessions"."created well";
                } else $this->message = $argument.'_sessions not done there was some errors  :'.mysqli_error($this->conn);
                
            } else $this->message = $argument.' failed to add primary key  :'.mysqli_error($this->conn);
        } else $this->message = $argument.' sessions Not Created : '.mysqli_error($this->conn);
        // creation of the table that will keep generated codes for user accounts
        
        $this->visits = 
        
        $this->sql = "CREATE TABLE IF NOT EXISTS `account_codes`(
                         `account_codes_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                         `type` int(11),
                         `codes` int(11),
                         `user_id` int(11),
                         `activation_link` TEXT,
                         `used` int(1)
                    )";
        if(mysqli_query($this->conn,$this->sql)){
            $this->status = true;
            $this->message = "account_codes created well";
        } else {
            $this->status = false;
            $this->message = "account_codes not craeted".mysqli_error($this->conn);
        }
        
        $this->conn->close();
        $this->conn = 0;
        
        // assignment of data tables as pipObjects
        $this->visits = new admin($this->session_name,$this->session_name."_id",$this->con);
        $this->useraccounts = new admin($this->session_name."_acounts",$this->session_name."_acounts_id",$this->con);
        $this->user_logs = new admin($this->session_name."_logs",$this->session_name."_logs_id",$this->con);
        $this->user_sessions = new admin($this->session_name."_sessions",$this->session_name."_sessions_id",$this->con);
        $this->account_codes = $this->table("account_codes");
        
        $this->useraccounts->child([$this->session_name,$this->session_name."_logs"],
                                   [$this->session_name."_id",$this->session_name."_logs_id"],
                                   ["logged_in",$this->session_name."_logs_user"])
            ->child[1]->GET_TAB()->child([$this->session_name."_sessions"],[$this->session_name."_sessions_id"],["session_id"]);
        
        $this->visits->child([$this->session_name."_sessions"],[$this->session_name."_sessions_id"],["session_id"])->parent__([$this->session_name."_acounts"],[$this->session_name."_acounts_id"],["logged_in"]);
        
        $this->user_logs->parent__([$this->session_name."_acounts"],[$this->session_name."_acounts_id"],[$this->session_name."_logs_user"]);
        
        
        if(isset($_SESSION[$this->session_name])){
             $this->user_session_id = $_SESSION[$this->session_name."_id"];
             $this->user_session_name = $_SESSION[$this->session_name];
             if($this->user_session_id){
                 $this->logged_in = true;
                 $this->user_session_present = 1;
             } else {
                 $this->logged_in = false;
                 $this->user_session_present = 0;
             }
          } 
        else {
            $this->logged_in = false;
            $this->user_session_present = 0;
        }
        
        //checking if there are some users to add one 
        if($this->count()==0){
            $this->register("test@test.test","tester","andtester","0123456789","0123456789@#","the_tester","none","none");
            $this->activate();
        }
 	}
    public function con($con){
        $this->con = $con;
    }
    //a function to count list of users in the database
    public function count(){
        return $this->useraccounts->counts();
    }
    // function that will keep every user visiting the system where $pagesURL is the url or a name of the page or a file,
    // $location is the geolocation of the visitor in form of LATITUDExLONGITUDE format 
    // and $screen is the resolution of the screen in WxH format.
    public function visit($pagesURL,$location,$screen){
        session_set_save_handler('PIPCLENTS::open_session',
                                 'PIPCLENTS::close_session',
                                 'PIPCLENTS::read_session',
                                 'PIPCLENTS::write_session',
                                 'PIPCLENTS::destroy_session',
                                 'PIPCLENTS::clean_session');
        session_start();
        $Visit_ok = false;
        if($this->visits->add_current_time_(['location',
                                             'screen',
                                             'session_id',
                                             'page',
                                             'logged_in'],
                                          [$location,
                                           $screen,
                                           $this->user_session_id,
                                           $pagesURL,
                                           $this->user_session_present],
                                          [0,1,2,3,4],
                                          'time')){
           $Visit_ok = true;
        } else {
            $message = " operation Failed to make record of this visitor due to: <b> ${$this->visits->message} </b> with query ${$this->visits->sql} call the web master for further information";
            $Visit_ok = false;
        
        } 
        return $Visit_ok;
    }
    // function that will manage all user login information where $INPUT_ARRAY is the array of values to check the users credential 
    // input credential have following order 0 is the username, 1 is the password, 2 is the email address 
    public function login($INPUT_ARRAY){
        $this->conn = $this->_connection_();
        $userName = "";
        $passWord = "";
        $email = "";
        $loginOk = false;
        // variable that will kepp login type where 3 is invalid
        //"0 will use username password and email", and "1 will be username and password only",
        // then "2 will be email and password ";
        $login_rules = 0;
        
        // checking number of input given
        if(sizeof($INPUT_ARRAY)==3){
            // getting username in the first index of the given array
            if(isset($_POST[$INPUT_ARRAY[0]])){
               $userName =  stripslashes($_POST[$INPUT_ARRAY[0]]);
               $userName = mysqli_real_escape_string($this->conn,$userName);
            } else if(isset($_GET[$INPUT_ARRAY[0]])){
               $userName =  stripslashes($_GET[$INPUT_ARRAY[0]]);
               $userName = mysqli_real_escape_string($this->conn,$userName);
            } else {
               $userName =  stripslashes($INPUT_ARRAY[0]);
               $userName = mysqli_real_escape_string($this->conn,$userName);
            }

            // getting password in the second index of the given array

            if(isset($_POST[$INPUT_ARRAY[1]])){
                $passWord =  stripslashes($_POST[$INPUT_ARRAY[1]]);
                $passWord = mysqli_real_escape_string($this->conn,$passWord);
            }
            else if(isset($_GET[$INPUT_ARRAY[1]])){
                $passWord =  stripslashes($_GET[$INPUT_ARRAY[1]]);
                $passWord = mysqli_real_escape_string($this->conn,$passWord);
            } else {
               $passWord =  stripslashes($INPUT_ARRAY[1]);
               $passWord = mysqli_real_escape_string($this->conn,$passWord);
            }

            // getting email in the thirds index of the given array

            if(isset($_POST[$INPUT_ARRAY[2]])){
                $email =  stripslashes($_POST[$INPUT_ARRAY[2]]);
                $email = mysqli_real_escape_string($this->conn,$email);
            }
               else if(isset($_GET[$INPUT_ARRAY[2]])){
                $email =  stripslashes($_GET[$INPUT_ARRAY[2]]);
                $email = mysqli_real_escape_string($this->conn,$email);
            } else {
               $email =  stripslashes($INPUT_ARRAY[2]);
               $email = mysqli_real_escape_string($this->conn,$email);
            }
            
            if($passWord=="0"){
                $loginOk = false;
                $this->message = "It is a must to provide password";
            } else if(($userName=="0")&&($email=="0")){
                $loginOk = false;
                $this->message = "username or email provided must be valid";
            } else if($userName=="0"){
                $login_rules = 2;
                $this->message = "login using email and password";
                $loginOk = true;
            } else if($email=="0"){
                $loginOk = true;
                $this->message = "login using username and password";
                $login_rules = 1;
            } else {
                $loginOk = true;
                $login_rules = 0;
            }
        }
        else if(sizeof($INPUT_ARRAY)==2){
             // getting username in the first index of the given array
            if(isset($_POST[$INPUT_ARRAY[0]])){
               $userName =  stripslashes($_POST[$INPUT_ARRAY[0]]);
               $userName = mysqli_real_escape_string($this->conn,$userName);
            } else if(isset($_GET[$INPUT_ARRAY[0]])){
               $userName =  stripslashes($_GET[$INPUT_ARRAY[0]]);
               $userName = mysqli_real_escape_string($this->conn,$userName);
            } else {
               $userName =  stripslashes($INPUT_ARRAY[0]);
               $userName = mysqli_real_escape_string($this->conn,$userName);
            }
             
            // getting password in the second index of the given array

            if(isset($_POST[$INPUT_ARRAY[1]])){
                $passWord =  stripslashes($_POST[$INPUT_ARRAY[1]]);
                $passWord = mysqli_real_escape_string($this->conn,$passWord);
            }else if(isset($_GET[$INPUT_ARRAY[1]])){
                $passWord =  stripslashes($_GET[$INPUT_ARRAY[1]]);
                $passWord = mysqli_real_escape_string($this->conn,$passWord);
            } else {
               $passWord =  stripslashes($INPUT_ARRAY[1]);
               $passWord = mysqli_real_escape_string($this->conn,$passWord);
            }
             
             if($passWord=="0"){
                $loginOk = false;
                $this->message = "It is a must to provide password";
            } else if($userName=="0"){
                $loginOk = false;
                $this->message = " username provided must be valid";
            } else {
                $loginOk = true;
                $this->message = "login using username and password";
                $login_rules = 1;
            }
             
             
         }
        else {
           $loginOk = false;
           $this->message = " login function must have an input with at least size of 2 like password or email ";
        }
        $user_ids = 0;
        if($loginOk){
            $errorsLOGSMessagesNum = 0;
            switch($login_rules){
                case 0:{
                    $this->message = "Login with all fields password, email, username";
                    if(strpos($email,"@")==""){
                        $this->message = "Email address not valid. Must contain at least one @ character";
                        $loginOk = false;
                        break;
                        
                    } 
                    else if(strpos($email,".")==""){
                        $this->message = "Email address not valid. Must contain at least a one dot(.)";
                        $loginOk = false;
                        break;
                    }
                    
                    $valid_userName = true;
                    $constr = "";
                    for($cc=0;$cc<sizeof($this->not_allowed_chars);$cc++){
                        $str_pos = strstr($userName,$this->not_allowed_chars[$cc]);
                        if($str_pos){
                            $constr = $constr."'".$this->not_allowed_chars[$cc]."' ";
                            $valid_userName = false;
                        } else echo $str_pos;
                        
                        if(($cc+1)==sizeof($this->not_allowed_chars)){
                          $this->message = "The username is not valid. Characters like ".$constr." are not allowed";
                        } else {
                            $constr = $constr." - ";
                        }
                    }
                    
                    $valid_password = false;
                    $constr = "";
                    
                    for($cc=0;$cc<sizeof($this->compulsery_chars);$cc++){
                        if(!$cc){
                           $constr = " ' " ;
                        }
                        $constr = $constr.$this->compulsery_chars[$cc].", ";
                        $str_pos = strstr($passWord,$this->compulsery_chars[$cc]);
                        if($str_pos){
                            $valid_password = true;
                            break;
                        }
                        
                        if(($cc+1)==sizeof($this->compulsery_chars)){
                          $constr = $constr." '";
                        }
                    }
                    
                    if(!$valid_userName){
                        $loginOk = false;
                        break;
                    }
                    else if(!$valid_password){
                        $loginOk = false;
                        $this->message = "The password is not valid. at least one of these characters: ".$constr." must be included";
                        break;
                    } 
                    else $this->message = "";
                    if($loginOk){
                         if($this->useraccounts->_gets__(["email"],[$email],[])->size==1){
                            if($this->useraccounts->_gets__(["username","email"],[$userName,$email],["AND"])->size==1){
                                $this_account = $this->useraccounts->_gets__(["username","email","password"],
                                                                [$userName,$email,$passWord],
                                                                ["AND","AND"]);
                                $user_ids = $this_account->AV[0][0]; 
                                $errorsLOGSMessagesNum = 0;
                                if($this_account->size==1){
                                    
                                    switch($this_account->AllValues[0]["activated"]){
                                        case 0:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your account is not yet activated please check out you email address for activation link";
                                            $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        case 1:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your are successfully loged in";
                                            $errorsLOGSMessagesNum = 3;
                                            break;
                                        }
                                        case 2:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your account is blocked please contact the wbmaster for help";
                                            $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        case 3:{
                                             $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." Your account is temporarily inavailable please contact the web master for help";
                                             $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        default: {
                                            $this->message = "Unkown login feed back";
                                            $errorsLOGSMessagesNum = 1000;
                                            break;
                                        }
                                    }
                                    
                                    
                                }
                                else if($this_account->size>1){
                                    
                                  $this->message = "System Error: the email address '".$email."' have same problem with the system you have to contact the web master for more informations";
                                  $errorsLOGSMessagesNum = 7;
                                }
                                else {
                                  $this->message = "The email address '".$email."', and '".$userName."'  was found but doesn't match the password provided";
                                  $errorsLOGSMessagesNum = 5;
                                }
                            }
                            else if(($this->useraccounts->_gets__(["username","email"],[$userName,$email],["AND"])->size>1)){
                               $this->message = "System Error: this account with '".$email."' have same problem with the system you have to contact the web master for more informations";
                               $errorsLOGSMessagesNum = 7;
                               $user_ids = $this->useraccounts->_gets__(["username","email"],[$userName,$email],["AND"])->AV[0][0];
                            }
                            else {
                                $this->message = "The email address '".$email."' was found but doesn't match with the'".$userName."' username provided"; 
                                $errorsLOGSMessagesNum = 6;
                                $user_ids = $this->useraccounts->_gets_(["email"],[$email])->AV[0][0];
                                 
                            }
                         }
                         else if($this->useraccounts->_gets__(["email"],[$email],[])->size>1){
                             $this->message = "System Error: the email address '".$email."' have same problem with the system you have to contact the web master for more informations";
                             $errorsLOGSMessagesNum = 7;
                             $user_ids = $this->useraccounts->_gets_(["email"],[$email])->AV[0][0];
                         }
                         else {
                             $this->message = "The email address '".$email."' was not found in the system";
                             $errorsLOGSMessagesNum = 6;
                         }
                    }
                    break;
                }
                case 1:{
                    $valid_userName = true;
                    $constr = "";
                    for($cc=0;$cc<sizeof($this->not_allowed_chars);$cc++){
                        $str_pos = strstr($userName,$this->not_allowed_chars[$cc]);
                        if($str_pos){
                            $constr = $constr."'".$this->not_allowed_chars[$cc]."' ";
                            $valid_userName = false;
                        } else echo $str_pos;
                        
                        if(($cc+1)==sizeof($this->not_allowed_chars)){
                          $this->message = "The username is not valid. Characters like ".$constr." are not allowed";
                        } else {
                            $constr = $constr." - ";
                        }
                    }
                    
                    $valid_password = false;
                    $constr = "";
                    
                    for($cc=0;$cc<sizeof($this->compulsery_chars);$cc++){
                        if(!$cc){
                           $constr = " ' " ;
                        }
                        $constr = $constr.$this->compulsery_chars[$cc].", ";
                        $str_pos = strstr($passWord,$this->compulsery_chars[$cc]);
                        if($str_pos){
                            $valid_password = true;
                            break;
                        }
                        
                        
                        if(($cc+1)==sizeof($this->compulsery_chars)){
                          $constr = $constr." '";
                        }
                    }
                    
                    if(!$valid_userName){
                        $loginOk = false;
                        break;
                    }
                    else if(!$valid_password){
                        $loginOk = false;
                        $this->message = "The password is not valid. at least one of these characters: ".$constr." must be included";
                        break;
                    }
                    else $this->message = "";
                    if($loginOk){
                        $this_userValues = $this->useraccounts->_gets__(["username"],[$userName],[]);
                        if($this_userValues->size==1){
                            $this_account = $this->useraccounts->_gets__(["username","password"],[$userName,$passWord ],["AND"]);
                            if($this_account->size==1){
                                $user_ids = $this_account->AV[0][0];
                                switch($this_account->AllValues[0]["activated"]){
                                        case 0:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your account is not yet activated please check out you email address for activation link";
                                            $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        case 1:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your are successfully loged in";
                                            $errorsLOGSMessagesNum = 3;
                                            break;
                                        }
                                        case 2:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your account is blocked please contact the webmaster for help";
                                            $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        case 3:{
                                             $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." Your account is temporarily inavailable please contact the web master for help";
                                             $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        default: {
                                            $this->message = "Unkown login feed back";
                                            $errorsLOGSMessagesNum = 1000;
                                            break;
                                        }
                                    }
                            } else {
                                $this->message = "The username ".$userName." was found but doesn't match with the provided password";
                                $errorsLOGSMessagesNum = 5;
                            }
                        } 
                        else{
                            $this->message = "The username ".$userName." was not found in the system ";
                            $errorsLOGSMessagesNum = 6;

                        }
                    }
                    break;
                }
                case 2:{
                   if(strpos($email,"@")==""){
                        $this->message = "Email address not valid. Must contain at least one @ character";
                        $loginOk = false;
                        break;
                        
                    } 
                    else if(strpos($email,".")==""){
                        $this->message = "Email address not valid. Must contain at least a one dot(.)";
                        $loginOk = false;
                        break;
                    }
                    $valid_password = false;
                    $constr = "";
                    for($cc=0;$cc<sizeof($this->compulsery_chars);$cc++){
                        if(!$cc){
                           $constr = " ' " ;
                        }
                        $constr = $constr.$this->compulsery_chars[$cc].", ";
                        $str_pos = strstr($passWord,$this->compulsery_chars[$cc]);
                        if($str_pos){
                            $valid_password = true;
                            break;
                        }
                        
                        
                        if(($cc+1)==sizeof($this->compulsery_chars)){
                          $constr = $constr." '";
                        }
                    }
                    if(!$valid_password){
                        $loginOk = false;
                        $this->message = "The password is not valid. at least one of these characters: ".$constr." must be included";
                        break;
                    } else $this->message = "";
                    
                    if($loginOk){
                        $this_account = $this->useraccounts->_gets__(["email"],[$email],[]);
                        if($this_account->size==1){
                            $this_account = $this->useraccounts->_gets__(["email","password"],[$email,$passWord],["AND"]);
                            if($this_account->size==1){
                                $user_ids = $this_account->AV[0][0];
                                switch($this_account->AllValues[0]["activated"]){
                                        case 0:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your account is not yet activated please check out you email address for activation link";
                                            $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        case 1:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your are successfully loged in";
                                            $errorsLOGSMessagesNum = 3;
                                            $user_ids = $this_account->AllValues[0][0];
                                            break;
                                        }
                                        case 2:{
                                            $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." your account is blocked please contact the webmaster for help";
                                            $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        case 3:{
                                             $this->message = $this_account->AllValues[0]["fname"]." ".$this_account->AllValues[0]["lname"]." Your account is temporarily ina available please contact the web master for help";
                                             $errorsLOGSMessagesNum = 4;
                                            break;
                                        }
                                        default: {
                                            $this->message = "Unkown login feed back";
                                            $errorsLOGSMessagesNum = 1000;
                                            break;
                                        }
                                    }
                            } else {
                              $this->message = "The email address '".$email."' has been found, but it is not matching with the password given";
                                $errorsLOGSMessagesNum = 5;
                            }
                        } 
                        else if($this_account->size>1){
                            $this->message = "System Error: the email address '".$email."' have same problem with the system you have to contact the web master for more informations"; 
                            $errorsLOGSMessagesNum = 7;
                        } 
                        else {
                            $this->message = " this email:'".$email."' is not found in the system";
                            $errorsLOGSMessagesNum = 6;
                        }
                    }
                    break; 
                }
                case 3:{
                    $this->message = "invalid login";
                    $errorsLOGSMessagesNum = 1000;
                    $user_ids = -1000;
                    break;
                }
                default:{
                    $this->message = "invalid login";
                    $errorsLOGSMessagesNum = 1000;
                    $user_ids = -1000;
                    break;
                }
            }
            
            $this->user_logs->add_current_time_([$this->session_name."_logs_type",$this->session_name."_logs_user"],
                                                [$errorsLOGSMessagesNum,$user_ids],
                                                [0,1],$this->session_name."_logs_time");
            if($errorsLOGSMessagesNum==3){
                 $_SESSION[$this->session_name] = 
                     $this->useraccounts->_gets__(["email","password"],[$email,$passWord],["AND"])->AV[0]["email"];
                 $_SESSION[$this->session_name."_id"] = 
                     $this->useraccounts->_gets__(["email","password"],[$email,$passWord],["AND"])->AV[0][0];
                 $this->user_session_id = $_SESSION[$this->session_name."_id"];
                 $this->user_session_name = $_SESSION[$this->session_name];
                 $this->logged_in = true;
                 $this->user_session_present = 1;
              } 
            else {
                $loginOk = false;
            }
            
        }
        $this->conn->close();
        $this->conn = 0;
        return $loginOk;
    }
    // function for logging out of the system
    public function logout(){
        if($this->user_logs->add_current_time_([$this->session_name."_logs_type",$this->session_name."_logs_user"],
                                                [19,$this->user_session_id],
                                                [0,1],$this->session_name."_logs_time")){
                if(session_destroy()){
                    $this->message = $this->errorsLOGSMessages[19];
                    return true;
                } else {
                    return false;
                }
            } else{
                $this->message = $this->user_logs->message;
                return false;
        }
    }
    public function SET_RULES($not_allowed,$compulsery){
        $this->not_allowed_chars = $not_allowed;
        $this->compulsery_chars = $compulsery;
    }
    // function to use when you need to connect a user with the other tables ,
    //$TABLES is the name of the table to connect with( admin datatype), 
    //$TYPE is a type of connection where 0 is to connect directly where a user will have single foreign key of the table id
    //1 is to connect with multiple row of the second table where we will create a second table to hold that activities
    // 2 is to connect with multiple row table where all we will not create a new table instead the foreign key will be the databracket of the defined primary key of the table
    public function connect($TABLE,$TYPE = 0){
        //$this->sql = "CREATE TABLE ``"
        if($TABLE instanceof admin){
            switch($TYPE){
                case 0:{
                    $this->sql = "CREATE TABLE IF NOT EXISTS `".$this->session_name.$TABLE->table_name."`(
                         `".$this->session_name.$TABLE->table_name."_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT`,
                         `".$TABLE->primary_key."` BIGINT(200),
                         `".$this->session_name."_id` BIGINT(200)
                    )";
                    break;
                }
                case 1:{
                    $this->sql = "CREATE TABLE IF NOT EXISTS `".$this->session_name.$TABLE->table_name."`(
                         `".$this->session_name.$TABLE->table_name."_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT`,
                         `".$TABLE->primary_key."` BIGINT(200),
                         `".$this->session_name."_id` BIGINT(200)
                    )";
                    break;
                }
                case 2:{
                    $this->sql = "CREATE TABLE IF NOT EXISTS `".$this->session_name.$TABLE->table_name."`(
                         `".$this->session_name.$TABLE->table_name."_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT`,
                         `".$TABLE->primary_key."` BIGINT(200),
                         `".$this->session_name."_id` BIGINT(200)
                    )";
                    break;
                }
            }
        } else {
            $this->message = "Table provided is not valid";
        }
        return $this;
    }
    // function to get list of users
    public function _gets_($fields=[],$values=[],$conds=[],$order="",$start=-1,$lenght=0){
        if(is_array($fields)){
            if(sizeof($fields)==0){
                $rets = $this->useraccounts->_gets_($this->user_session_id);
                $this->sql = $this->useraccounts->sql;
                return $rets;
            } else {
                $rets = $this->useraccounts->_gets_($fields,$values,$conds,$order,$start,$lenght);
                $this->sql = $this->useraccounts->sql;
                return $rets;
            }
        } else if($fields=="*"){
            $rets = $this->useraccounts->_gets_();
            $this->sql = $this->useraccounts->sql;
            return $rets;
        } else {
            $rets = $this->useraccounts->_gets_($fields,$values,$conds,$order,$start,$lenght);
            $this->sql = $this->useraccounts->sql;
            return $rets;
        }
    }
    // a function to fetch latest records in the table
    public function last($field = "",$number = 1){
        $rets = $this->useraccounts->last($field,$number);
        $this->sql = $this->useraccounts->sql;
        return $rets;
    }
    // a function to fecth first records in the table
    public function first($field = "", $number = 1){
        $rets = $this->useraccounts->first($field,$number);
        $this->sql = $this->useraccounts->sql;
        return $rets;
    }
    // a public function to gets all users logs if $userid = 0; and a specified user by a user_id
    public function profilePic($data = "",$imgF = ""){
        if($data instanceof PIP_Array){
            if($data->index("profile_pic")&&$data->index("profile_pic")){
                if(is_file($imgF.$data->JS()->profile_pic)) return $imgF.$data->JS()->profile_pic;
                else if(is_file($imgF.$data->JS()->profile_pic.".jpg")) return $imgF.$data->JS()->profile_pic.".jpg";
                else if(is_file($imgF.$data->JS()->profile_pic.".png")) return $imgF.$data->JS()->profile_pic.".png";
                else return $imgF."sys_files/avatar/".pipStr(lcfirst($data->JS()->fname))->sub(0).".png";
            } else return $imgF."sys_files/user.png";
        } else if(is_string($data)){
            return $this->profilePic($this->_gets_(),$data);
        } else {
            return $imgF."sys_files/user.png";
        }
    }
    public function accounts($userId = 0){
        $acc = 0;
        if($userId==0)
            $acc  = $this->useraccounts->_gets_();
        else $acc  = $this->useraccounts->_gets_($userId);
        $this->sql = $this->useraccounts->sql;
        return $acc;
    }
    public function logs($userId = 0){
        $logs = 0;
        if($userId==0)
            $logs = $this->user_logs->_gets_();
        else 
           $logs = $this->user_logs->_gets_([$this->session_name."_logs_user"],[$userId]); 
        $this->sql = $this->user_logs->sql;
        return $logs;
    }
    // a public function to get all users visits if $userid = 0; and a specified user by a user_id
    public function visits($userId = 0){
        $vis = 0;
        if($userId==0)
            $vis =  $this->visits->_gets_();
        else $vis  = $this->visits->_gets_([$this->session_name."_acounts"],[$userId]);
        $this->sql = $this->visits->sql;
        return $vis;
    }
    // a public function to get all users sessions if $userid = 0; and a specified user by a user_id
    public function sessions($userId = 0){
        $sess = 0;
        if($userId==0)
            $sess = $this->user_sessions->_gets_();
        else {
            
            $vis = $this->visits($userId);
            $vis_ids = $vis->Cols("session_id");
            $vis_keys = $vis->ColsKeys($this->session_name."_session_id");
            $vis_conds = $vis->ColsKeys("OR");
            
            $sess = $this->user_sessions->_gets_($vis->Cols("session_id"),
                                                 $vis->ColsKeys($this->session_name."_session_id"),
                                                 $vis->ColsKeys("OR"));
        }
        $this->sql = $this->user_sessions->sql;
        return $sess;
    }
    // a function of recovering a lost password where userId is the parameter specifying email , tel or userid of the account
    public function recover($userId){
        $rets = false;
        $this_user = $this->useraccounts->_gets_([$userId,$userId,$userId],["email","tel",$this->session_name."_id"],["OR","OR"]);
        if($this_user->size==0){
            $this->message = "this account is no longer exist in the system, may be it is removed or not existed";
            $rets = false;
        } else if($this_user->size==1){
            if($this_user->JS(0)->activated==1){
                
            } else {
                $this->message = $this_user->JS(0)->fname." ".$this_user->JS(0)->lname." this account is not activated check your email(".$this_user->JS(0)->email.") for activation mesage";
                $rets = false;
            }
        } else {
            $this->message = "Internal system error Contact support for more information";
            $rets = false;
        }
        
        return $rets;
    }
    // a function to register a user
    public function register($email,$fname,$lname,$tel,$password,$username="",$profile_pic = "",$cover_pic = ""){
        $cehcking = ["email","tel"];
        $vals = [$email,$tel];
        if($tel==""){
          $cehcking = ["email"];
          $vals = [$email];  
        }
        
        $fields = ["fname","lname","email","password","tel","activated","profile_pic","cover_pic","username"];
        $values = [$fname,$lname,$email,md5($password),$tel,"0",$profile_pic,$cover_pic,$username];
        
        $added = $this->useraccounts->register_($cehcking,$vals,$fields,$values);
        if($added>0){
            $this->sql = $this->useraccounts->sql;
            if($this->user_logs->_add_(["administration_logs_type","administration_logs_user"],
                                    [1,$added],
                                    'administration_logs_time')){
                
            } else {
                $this->message  = $this->user_logs->message;
            }
            return $added;
        } else {
            $this->message = $this->useraccounts->message;
            $this->sql = $this->useraccounts->sql;
            if($this->user_logs->_add_(["administration_logs_type","administration_logs_user"],
                                    [2,$added],
                                    'administration_logs_time')){
                
            } else {
                $this->message  = $this->user_logs->message;
            }
            return 0;
        }
    }
    // a function to remove a user 
    public function remove($user_id){
        if(is_numeric($user_id)){
            if(intval($user_id)==intval($this->user_session_id)){
                $this->message = "a user can not remove him/herself!!";
                return false;
            } else {
                $this->visits->delete($this->session_name,$user_id);
                $this->useraccounts->delete_($user_id);
                $this->user_logs->delete($this->session_name."_logs_user",$user_id);
                //$this->user_sessions->delete($field,$user_id);
                return true;
            }
        } else {
           $this->message = "wrog input provided";
           return false; 
        } 
        
    }
    
    // public function to send an email and sms to the user with userId provided, Whre $both is to specify which to send 0 is to send email and SMS, 1 email only, 2 sms only 
    public function sendMessage($userId,$header,$messageSMS,$messageEmail, $both = "0"){
        $this_user = $this->useraccounts->_gets_([$this->session_name."_id"],[$userId]);
        if($this_user->size){
            switch($both){
                case "0":{
                    if($this->validMail($this_user->JS(0)->email)){
                        if($this->sendEmail($this_user->JS(0)->email,$header,$messageEmail)){
                            if($this->validTel($this_user->JS(0)->tel)){
                                   if($this->sendSMS($this_user->JS(0)->tel,$header,$messageSMS)){
                                        $this->message = "Sending  of email and SMS success";
                                        return true;
                                    } else {
                                        $this->message = "Sending of SMS failed!".$this->message;
                                        return false;
                                    } 
                             }
                        } else {
                            $this->message = "Sending of email failed!".$this->message;
                            return false;
                        }
                    } else if($this->validTel($this_user->JS(0)->tel)){
                        if($this->sendSMS($this_user->JS(0)->tel,$header,$messageSMS)){
                            $this->message = "Sending of SMS success";
                            return true;
                        } else {
                            $this->message = "Sending of SMS failed!".$this->message;
                            return false;
                        }
                    } else {
                        $this->message = "both address are not valid!";
                        return false;
                    }
                    break;
                }
                case "1":{
                    if($this->validMail($this_user->JS(0)->email)){
                        if($this->sendEmail($this_user->JS(0)->email,$header,$messageEmail)){
                            $this->message = "Sending  of email success";
                            return true;
                        } else {
                            $this->message = " Sending of email failed!".$this->message;
                            return false;
                        }
                    } else {
                        $this->message = "email address are not valid!";
                        return false;
                    }
                }
                case "2":{
                    if($this->validTel($this_user->JS(0)->tel)){
                        if($this->sendSMS($this_user->JS(0)->tel,$header,$messageSMS)){
                            $this->message = "Sending of SMS success";
                            return true;
                        } else {
                            $this->message = "Sending of SMS failed!".$this->message;
                            return false;
                        }
                    } else {
                        $this->message = "phone number given is not valid!";
                        return false;
                    }
                }
                default:{
                    return $this->sendMessage($userId,$header,$messageSMS,$messageEmail,"0");
                }
            }
        } 
        else {
           $this->message = "The user given is not found in the system";
           return false;
        }
    }
    
    public function validMail($email){
        return (strpos($email,"@")&&strpos($email,"."));
    }

    public function validTel($tel){
        return !(intval($tel)==0);
    }
    
    public function sendSMS($tel,$header,$message){
        return true;
    }
    
    public function sendEmail($email,$header,$message){
        return true;
    }
    
    public function ActivationCodes($userId){
        $all_account_codes = $this->account_codes->_gets_();
        if($all_account_codes->size>0){
            $all_account_codes = $all_account_codes->_gets_("used","1");
            if($all_account_codes->size){
                
            } 
        } else {
            
        }
    }
    
    private static function open_session() {
        $this->conn = $this->_connection_();
        return true;
    }
    private static function close_session() {
        return mysqli_close($this->conn);
    } 
    private static function read_session($sid) {
         $q = sprintf('SELECT `'.$this->session_name.'_sessions_data` 
                       FROM `'.$this->session_name.'` WHERE `'.$this->session_name.'_sessions_id`="%s"',
                       mysqli_real_escape_string($this->conn,$sid));
         $r = mysqli_query($this->conn, $q);
         if (mysqli_num_rows($r) == 1) {
             list($data) = mysqli_fetch_array($r, MYSQLI_NUM);
             return $data;
         } 
         else { 
            return '';
         }

     }
    private static function write_session($sid, $data) {
        $q = sprintf('REPLACE INTO '
                     .$this->session_name.
                     ' ('
                     .$this->session_name.'_sessions_id, 
                     '.$this->session_name.'_sessions_data) VALUES ("%s", "%s")',
        mysqli_real_escape_string($this->conn, $sid), mysqli_real_escape_string($this->conn, $data));
        $r = mysqli_query($this->conn, $q);
        return mysqli_affected_rows($this->conn);
    }
    private static function destroy_session($sid) {
        $q = sprintf('DELETE FROM '.$this->session_name.' WHERE '.$this->session_name.'_sessions_id="%s"', mysqli_real_escape_string($this->conn, $sid));
        $r = mysqli_query($this->conn, $q);
        $_SESSION = array();
        return mysqli_affected_rows($this->conn);
    } 
    private static function clean_session($expire) {
        $q = sprintf('DELETE FROM `'.$this->session_name.'`  
                      WHERE DATE_ADD('.$this->session_name.'_time, INTERVAL %d SECOND) < NOW()',
                     (int) $expire);
        $r = mysqli_query($this->conn,$q);
        return mysqli_affected_rows($this->conn);
     }
    public function message(){
        echo $this->message;
    }
    // a function to check if the logged in use has an access or still exist in the system
    public function hasAccess($ids = 0){
        if(is_numeric($ids)){
            if($ids>0){
                $the_user = $this->useraccounts->_gets_($ids);
                if($the_user->size==1){
                  if(is_numeric($the_user->JS()->activated))
                      if(intval($the_user->JS()->activated)==1){
                          return true;
                      } 
                }
            } else {
                if($this->logged_in){
                    $this_one = $this->_gets_();
                    if($this_one->size==1){
                        if(is_numeric($the_user->JS()->activated))
                            if(intval($the_user->JS()->activated)==0)
                                return true;
                    }
                }
            }   
        }
        return false;
    }
    
    public function activate($ids = 1){
        $this->useraccounts->edit("activated","1",$ids);
    }
    /* this function must be developed on the level where we will have the smartest one with ALTER TABLE SQL*/
    public function _child_(&$TABLES,$PRIMARY = ""){
        $this->useraccounts->_child_($TABLES,$PRIMARY = "");
        return $this;
    }
    
    public function _parent_(&$TABLES,$PRIMARY = ""){
        $this->useraccounts->_parent_($TABLES,$PRIMARY = "");
        return $this;
    }
 }
/* THE Public functions to create list of clients */
function CLIENT_LIST($ACCOUNTS_names,$conn){
    $ACCOUNTS = array();
    for($ii=0;$ii<sizeof($ACCOUNTS_names);$ii++){
      $ACCOUNTS[$ACCOUNTS_names[$ii]] = new PIPCLENTS($ACCOUNTS_names[$ii],$conn);
      $ACCOUNTS[$ii] = new PIPCLENTS($ACCOUNTS_names[$ii],$conn);
    }
    return $ACCOUNTS;
}
/* THE OF DATAS IN THE DATABASE IN FORM OF MULTIDIMENTIONAL ARRAY AND ALL OF THEIR ACTIVITIES */
class PIP_Array{
    // public variable to keep original array 
     public $AllValues = array();
    // public variable to keep dimension of an array
     public $Dimension;
    // public variable to keep dimension of array in array format( [4][3][6])
     public $Dimension_array;
    // public variable to keep an array in json format
     public $jsons;
    // publlic variable to keep an array in reversed order
     public $reversed;
    // public variable to keep the size of an array
     public $size;
    // public array to keep all array keys
    public $keys = array();
    // a string keys only
    public $_keys = array();
    // a numeric keys only
    public $keys_ = array();
    // public variables to simplifie typing of AllValues , reversedValues and jsons to AV, RV, JV respectively.
    public $AV = array();
    public $RV = array();
    public $JV;
    // public variables with the same like AV, RV but with no numeric indexes
    public $_AV = array();
    public $_RV = array();
    // public variables with the same like AV, RV but with numeric indexes only
    public $AV_ = array();
    public $RV_ = array();
    // a public variable to keep all error messages happened eary
    public $message = "No thing wrong";
    // an array to keep the unsinged indexed array with the defaultone
    private const unnamed = ["zero","one","two","three","four","five","six","seven","eight","nine"];
    
     function __construct($Array = ""){
         $rr = $Array;
         if(is_string($rr))
             $rr = [];
         if(isset($Array["data_records"])){ 
             if($Array["data_records"]==0){
                 $this->message = $Array[0]["message_returneds_failed_pip_array"];
                 $this->size = 0 ;
                
                 
                 $this->AllValues = [];
                 $this->reversedValues = [];


                 $this->AV = $this->AllValues;
                 $this->RV = $this->reversedValues;
                 
                  $this->jsons = json_encode($this->AV);
                 $this->JV = $this->jsons;
                 $this->message = "data recorded success with empty result";
                 
             } 
             else {
                 $this->message = "data recorded success";
                 $this->init($rr,1);
             }
         } 
         else {
             $this->message = " data are not from database directly ";
             $this->init($rr);
         }
     }
    //a function for initiation of the object
    
     public function init($rr = "",$rsize = 0){
         if(is_string($rr))
             $rr = [];
         $this->size = sizeof($rr);
         if($rsize){
             $this->size = sizeof($rr)-1;
         }
         if($this->size>0)
            $this->keys = array_keys($rr[0]);
         $this->_keys = array();
         $this->AllValues = [];
         for($ii=0;$ii<$this->size;$ii++){
             array_push($this->AllValues,array());
         }
         
         for($ii=0;$ii<sizeof($this->keys);$ii++){
             if(!is_numeric($this->keys[$ii])){
                 if(!contains_arr($this->_keys,$this->keys[$ii])){
                     array_push($this->_keys,$this->keys[$ii]);
                 }
             } else {
                 if(!contains_arr($this->keys_,$this->keys[$ii]))
                     array_push($this->keys_,$this->keys[$ii]);
             } 
         }
         
         if(sizeof($this->keys_)>sizeof($this->_keys)){
             for($ii=sizeof($this->_keys);$ii<sizeof($this->keys_);$ii++){
                     array_push($this->_keys,self::index_accoc($this->keys_[$ii]));
                     for($iii=0;$iii<$this->height();$iii++){
                         if(isset($rr[$iii][$this->keys_[$ii]]))
                            $rr[$iii][$this->_keys[$ii]] = $rr[$iii][$this->keys_[$ii]];
                     }
             }
         } 
         else if(sizeof($this->keys_)<sizeof($this->_keys)){
             for($ii=sizeof($this->keys_);$ii<sizeof($this->_keys);$ii++){
                     array_push($this->keys_,$ii);
                     for($iii=0;$iii<$this->height();$iii++)
                         //$rr[$iii] = $rr[$iii][$this->_keys[$ii]];
                         array_push($rr[$iii],$rr[$iii][$this->_keys[$ii]]);
             }
         }
         
         //echo "<pre>".print_r($this->_keys,1)."</pre>";
         
         
         
         for($ii=0;$ii<sizeof($this->_keys);$ii++){
             for($iii=0;$iii<$this->size;$iii++){
                 if(isset($rr[$iii][$this->_keys[$ii]])){
                   array_push($this->AllValues[$iii],
                              $rr[$iii][$this->_keys[$ii]]);
                   $this->AllValues[$iii][$this->_keys[$ii]] 
                        = $rr[$iii][$this->_keys[$ii]];   
                 }
             }
         }
         
         
         $this->reversedValues = array_reverse($this->AllValues);
         
         $this->AV = $this->AllValues;
         $this->RV = $this->reversedValues;
         

         for($ff=0;$ff<$this->size;$ff++){
             $index_counter = 0;
             for($fff=0;$fff<sizeof($this->keys);$fff++){
                 if(!is_numeric($this->keys[$fff])){
                     if(isset($this->AV[$ff][$this->keys[$fff]])){
                            $this->_AV[$ff][$this->keys[$fff]] = 
                            $this->AV[$ff][$this->keys[$fff]];
                     }
                 }
                 else{
                     if(isset($this->AV[$ff][$index_counter])){
                         $this->AV_[$ff][$index_counter] = 
                         $this->AV[$ff][$this->keys[$fff]]; 
                         $index_counter++;
                     }
                 }     
             }
         }



         $this->jsons = json_encode($this->_AV);
         $this->_RV = array_reverse($this->_AV);
         $this->RV_ = array_reverse($this->AV_);
         //echo "<pre>".print_r($this->AV_,1)."</pre>";
         $this->JV = $this->jsons;
         return $this;
     }
    // a function to return the ids of the array 
    public function id($index = 0){
        if(($this->size)&&($this->size>=$index))
            return $this->AV[$index][0];
        else return 0;
    }
    // a static function to give the number an associated indexes
    public static function index_accoc($number){
        $number = "$number";
        $rets = "";
        for($ii=0;$ii<strlen($number);$ii++){
            $rets .= self::unnamed[intval(substr($number,$ii,1))]; 
        }
        return $rets;
    }
       // the function that is able to return values of an array excluding or including the value of which is equal to the index given on 
    // a 2 dimentional array given according to the type provided
    //INDEX is the index name or value of the array to be excluded or included in the new array
    //VALUE is the variable to be checked to match with the index provided 
    //TYPE is the string between `ONLY` and   `REMOVE` to include or exclude all 1d arrays with all datas like that respectively
     public function filterthis($INDEX,$VALUE,$TYPE="REMOVE"){
                        if(!$this->index($INDEX)) return pipArr([]);
                        $rets = array();
                        $error_mess = "";
                        
                        if($this->size>0){
                            if($this->AllValues[0][0]==-1){
                                return new PIP_Array($this->AllValues);
                            } else{
                                for($ii=0; $ii<$this->size; $ii++){
                                  if($TYPE=="REMOVE"){
                                      if(!($this->AllValues[$ii][$INDEX]==$VALUE)){
                                        array_push($rets,$this->AllValues[$ii]);
                                    }
                                  } else {
                                     //echo "<pre>".print_r($this->AllValues,1)."</pre>";
                                     if($this->AllValues[$ii][$INDEX]==$VALUE){
                                        array_push($rets,$this->AllValues[$ii]);
                                    } 
                                  }  
                                }
                            }
                            
                          } else {
                              return new PIP_Array($this->AllValues);
                          }
                          return new PIP_Array($rets);
                        }
                     
    // a public function to simplify the one above also to give it more power like reading arrays
    
    public function _gets_($INDEX, $VALUE = "", $TYPE = "REMOVE"){
        if($VALUE==""){
            return $this->filterthis(0,$INDEX,"O");
        } else {
            return $this->filterthis($INDEX,$VALUE,$TYPE);
        }
    }
     // the function that is able to return values of an array excluding reapeted values to the index given of  
    // a 2 dimentional array given
    // INDEX is the index of the array where its repeated datas will be eliminated 
     public function filterthis_distinct($INDEX = "", $lats = ""){
            $rets = array();
            if($this->width()==0){
                return $this;
            }
            if(strlen($INDEX)==0){
                if($this->height()>0){
                    $rets = $this->filterthis_distinct(0,"_");
                    for($ii=1;$ii<$rets->size;$ii++)
                      $rets = $rets->filterthis_distinct($ii,"_");
                    return $rets;
                } else {
                    return $this;
                }
            } 
            else if(is_string($INDEX)){
               for($ii=0;$ii<$this->size;$ii++){
                    $exist = false;
                    for($iii=0;$iii<$ii;$iii++){
                        if($this->AllValues[$ii][$INDEX]==$this->AllValues[$iii][$INDEX]){
                            $exist = true;
                        }
                    }
                    if(!$exist) array_push($rets,$this->AllValues[$ii]);
                }
                
            } 
            else if(is_numeric($INDEX)){
                if($this->width()<$INDEX){
                    return $this;
                } 
                else if($this->width()==0){        
                  return $this;   
                }
                else if($lats=="_"){
                    $lats_exist = $this->RowsKeys(false);
                    $vals_exist = $this->Rows($INDEX);
                    $newArr = array();
                    for($ii=0;$ii<$this->width();$ii++){
                        if(!contains_arr($newArr,$this->AV[$INDEX][$ii])){
                            array_push($newArr,$this->AV[$INDEX][$ii]);
                            $lats_exist[$ii] = true;
                        }     
                    }
                    
                    for($ii=0;$ii<$this->height();$ii++){
                        $newArr = array();
                        $index_counter = 0;
                        for($iii=0;$iii<$this->width();$iii++){
                            if($lats_exist[$iii]){
                                array_push($newArr,$this->AV[$INDEX][$iii]);
                                $newArr[$this->_keys[$index_counter]] = $this->AV[$INDEX][$iii];
                                $index_counter++;
                            }
                        }
                        array_push($rets,$newArr);
                    }
                } 
                else {
                    return $this->filterthis_distinct($this->keys[$INDEX]);
                }
            }
            else {
                $rets = array($rets);
            }
            return new PIP_Array($rets);
    }
    // this is a function to return a new 2d array with matching values of the 2d arrays given
    // PIP_ARRAY2 is the object to get all distinct data to be selected from
    // INDEX2 is the index of the PIP_ARRAY object to be selected distinctly from
    // INDEX1 is the index to be selected distinctly from
     public function filterthis_multiple($PIP_ARRAY2,$INDEX2,$INDEX1,$TYPES = "REMOVE"){
        $ARR = $this->filterthis_distinct($INDEX2);
        if($PIP_ARRAY2->size>0){
             $all = $PIP_ARRAY2->filterthis_distinct($INDEX1);
             for($ii=0;$ii<$all->size;$ii++){
                   $ARR = $ARR->filterthis($INDEX2,$all->AV[$ii][$INDEX1],$TYPES);
             }
             return new PIP_Array($ARR->AV);
        } else {
            if($TYPES=="REMOVE"){
               return new PIP_Array($ARR->AV); 
            } else {
               return $this->empty();
            }
        }
    }
    // a function similar to that of the above but with redundate data
     public function getsM($PIP_ARRAY2,$INDEX2,$INDEX1,$TYPES = "REMOVE"){
        $ARR = $this;
        $all = $PIP_ARRAY2;
        if($PIP_ARRAY2->size>0){
             for($ii=0;$ii<$all->size;$ii++){
                   $ARR = $ARR->filterthis($INDEX2,$all->AV[$ii][$INDEX1],$TYPES);
             }
             return new PIP_Array($ARR->AV);
        } else {
            if($TYPES=="REMOVE"){
               return new PIP_Array($ARR->AV); 
            } else {
               return $this->empty();
            }
        }
     }
    // a function to use to check if the given index is include in the indexes we have in the PIP_Array
    public function index($index){
        if($this->size>0){
            return isset($this->AV[0][$index]);
        } 
        else {
          $this->message = "the given object doesn't contain data";
          return false;   
        }
    }
    // a function to return the first index of the first occurence of the given value of the index
     public function indexOf($value,$index=0){
         $indx = -1;
         if($this->size==0){
             $this->message = "this PIP_Array is empty";
             return $indx;
         }
             
         if(!contains_arr($this->keys,$index)){
             $this->message = "the given index ($index) doen't exist in the given";
             return $indx;
         }
             
         for($ii=0;$ii<$this->size;$ii++){
             if($this->AV[$ii][$index]==$value){
                 $this->message = "the given value ($value) with index ($index) has been found at the position of ".$ii;
                 return $ii;
             } else {
                $this->message = "the given value ($value) with index 
                ($index) was not found"; 
             }
                 
         }
         return $indx;
     }
    // a function to return if the specified value is at the specified index position given of the given dictionnary index
    public function  isindexOf($value,$position,$index = 0){
        if($this->size==0){
            $this->message = "this PIP_Array is empty";
            return false;
        }
            
        if($this->size<$position){
            $this->message = "the given position is greater than this PIP_Array size";
            return false;
        }
        
        if($position<0){
            $this->message = "the given position must be a positive value";
            return false;
        }
        
        if(!contains_arr($this->keys,$index)){
             $this->message = "the given index ($index) doen't exist in this PIP_Array";
             return false;
         }
        if($this->indexOf($value,$index)==-1){
            return false;
        }
        
        if($this->AV[$position][$index]==$value){
            return true;
        } 
        else {
            $this->message = "the given position is not where the value ($value) of the index($index) is located";
            return false;
        }
    }
    // a function to empty the PIP_Array
     public function empty(){
        return new PIP_Array(array());
    }
     // this is a function to return the lowest value in the 2d  array in the specified index
    // INDEX is the index to be with others in the row
     public function lowest($INDEX){
         $lowest = $this->AllValues[0][$INDEX];
         for($ii=0;$ii<$this->size;$ii++){
             if($this->AllValues[$ii][$INDEX]<$lowest){
                 $lowest = $this->AllValues[$ii][$INDEX];
             }
         }
        return $lowest;
     }
    // a function to return a PIP_Array format 
    public function lowestP($INDEX){
         return $this->_gets_($INDEX,$this->lowest($INDEX),"O");
     }
      // this is a function to return the highest value in the 2d  array in the specified index
    // INDEX is the index to be with others in the row
     public function highest($INDEX = 0){
         if($this->size==0){
             return 0;
         }
         $highest = $this->AV[0][$INDEX];
         for($ii=0;$ii<$this->size;$ii++){
             if($this->AV[$ii][$INDEX]>$highest){
                 $highest = $this->AV[$ii][$INDEX];
             }
         }
        return $highest;
     } 
    // the function to return all highest values in PIP_Array format
     public function highestP($INDEX = 0){
         return $this->_gets_($INDEX,$this->highest($INDEX),"O");
     }
     // This is a function that will chech if a value given is included in the array given
    // where the INDEX is the index to check in and value is the value to be checked
     public function included($INDEX,$VALUE){
         $rets = false;
         for($ii=0;$ii<$this->size;$ii++){
             if(isset($this->AllValues[$ii][$INDEX]))
                 if($this->AllValues[$ii][$INDEX]==$VALUE){
                     $rets = true;
                     break;
                 }
         }
         return $rets;
     }
    // this iss a function to return all values that is less than the given value and index
     public function lessThan($value,$index = ""){
        $rets = array();
        if($index=="")
            $index = 0;
        for($ii=0;$ii<$this->size;$ii++){
            if(intval($value)>intval($this->AV[$ii][$index])){
               array_push($rets,$this->AV[$ii]);
            }
        }
        return new PIP_Array($rets);
     }
    // this iss a function to return all values that is less than the given value and index
     public function higherThan($value, $index = ""){
        $rets = array();
        if($index=="")
            $index = 0;
        for($ii=0;$ii<$this->size;$ii++){
            if(intval($value)<intval($this->AV[$ii][$index])){
               array_push($rets,$this->AV[$ii]);
            }
        }
        return new PIP_Array($rets);
     }
    // a function to order indexed numbers by this array where order is zero for croissant and decroissant  is something else
     public function OrderBy($index = 0, $order = 0){
         $rets = array();
         if($this->size==0)
             return $this;
         else {
             $temp = new PIP_Array($this->AV);
             for($ii=0;$ii<$this->size;$ii++){
                 array_push($rets,$temp->highestP($index)->AV[0]);
                 $temp = $temp->_gets_($index,$temp->highest($index));
             }
         }
         $rets = new PIP_Array($rets);
         if($order==0)
             return $rets->reverse();
         else return $rets;
     } 
    // this is a function to return json  like data of the array with the specified index
     public function JS($INDEX = 0){
         return new ARR_TO_JSON($this->AllValues,$INDEX);
     }
    // public function to print the given value
      public function printi($INDEX = 0,$ASSOC = "", $LENGTH = -10){
         if($ASSOC==""){
            $ASSOC = 0; 
         }
          
          
         if(!is_numeric($INDEX)){
             $ASSOC = $INDEX;
             $INDEX = 0;
         }
         if(isset($this->AllValues[$INDEX])){
             if(!isset($this->AllValues[$INDEX][$ASSOC])){
                 echo "<span style='color:red'> unkown </span>";
                 return;
             }
             if($LENGTH==-10)
                print_r($this->AllValues[$INDEX][$ASSOC]);
             else print_r(substr($this->AllValues[$INDEX][$ASSOC],0,$LENGTH));
         }
            
         else echo "<span style='color:red'> unkown </span>";
      }
      // a public function to display corrected structure of the this array
      public function List($index = -1){
          $prints = $this->AV;
          if($index>=0){
             $prints = $this->AV[$index];
          }
          echo "<pre>".print_r($prints,1)."</pre>";
      }
      // Name sorting function:
      protected function text_sort($x, $y,$v) {
            return strcasecmp($x[$v],$y[$v]);
      }
    // Grade sorting function:
    // Sort in DESCENDING order!
      protected function number_sort($x,$y,$v) {
          return ($x[$v] < $y[$v]);
      }
      // this is a function to sort all values by $index given where $txt_num is the choice btn number or text and 'n' is for numbers and sothing else is for text
      public function sortBy($index,$txt_num = ""){
          if($txt_num==""){
              if(is_numeric($index)){
                  uasort($this->AV, 'number_sort');
              } else {
                  uasort($this->AV, 'text_sort');
              }
          } else if($txt_num=="n"){
              uasort($this->AV, 'number_sort');
          } else uasort($this->AV, 'text_sort');
        return $this;
      }
      // a public function to reverse the main list of data
      public function reverse(){
          return new PIP_Array($this->RV);
      }
      // a public function to randomize the list of data
      public function random(){
          $retsArr = array();
          //$keys = array_keys($this->ARRY[$position]);
          $randomArr = array();
          for($ii=0; $ii<$this->size; $ii++){
              $cur = rand()%$this->size;
              while($this->isExist($randomArr,$cur)){
                  $cur = rand()%$this->size;
              }
              array_push($randomArr,$cur);
          }
          
          for($ii=0;$ii<$this->size;$ii++){
              $retsArr[$ii] = $this->AV[$randomArr[$ii]];
          }
          
          return new PIP_Array($retsArr);
      }
      // a function to add new user defined index which is not from database
      public function push($index,$name,$data){
          if($index<$this->height()){
             $this->AV[$index][$name] = $data;
             array_push($this->AV[$index],$data);
             return new PIP_Array($this->AV); 
          } else return $this;
      }
      // a function to add multiple data, the same as push 
      public function push_($table, $vals, $new_vals, $values = "",$fields1 = "",$all = false){
          if($table instanceof admin){
            if(is_array($vals)&&is_array($new_vals)){
              if(sizeof($vals)>sizeof($new_vals)){
                  $new_valsx = [];
                  for($ii=0;$ii<sizeof($vals);$ii++){
                      if(isset($new_vals[$ii])){
                         array_push($new_valsx,$new_vals[$ii]);
                         if($new_valsx[$ii]=="")
                             $new_valsx[$ii] = $vals[$ii];
                      }
                      else {
                         array_push($new_valsx,$vals[$ii]); 
                      }
                  }
                  return $this->push_($table,$vals,$new_valsx,$values,$fields1);
              }
              else if(sizeof($vals)<sizeof($new_vals)) {
                  $valsx = [];
                  for($ii=0;$ii<sizeof($new_vals);$ii++){
                      if(isset($vals[$ii])){
                         array_push($valsx,$vals[$ii]);
                         if($valsx[$ii]=="")
                             $valsx[$ii] = $new_vals[$ii];
                      }
                      else {
                         array_push($valsx,$new_vals[$ii]);
                      }
                  }
                  return $this->push_($table,$valsx,$new_vals,$values,$fields1);
              }
              else {
                 if(is_array($values)&&is_array($fields1)){
                     if(sizeof($values)>sizeof($fields1)){
                         $fields1x = [];
                         for($ii=0;$ii<sizeof($values);$ii++){
                             if(isset($fields1[$ii]))
                                 array_push($fields1x,$fields1[$ii]);
                             else $fields1x = $table->id();
                         }
                         return $this->push_($table,$vals,$new_vals,$values,$fields1x);
                     } else if(sizeof($values)<sizeof($fields1)){
                         $valuesx = [];
                         for($ii=0;$ii<sizeof($fields1);$ii++){
                             if(isset($values[$ii]))
                                 array_push($valuesx,$values[$ii]);
                             else array_push($valuesx,0);
                         }
                         return $this->push_($table,$vals,$new_vals,$valuesx,$fields1);
                     } else {
                         $new_rets = $this;
                         $CONDS = [];
                         for($ii=0;$ii<sizeof($values);$ii++){
                             array_push($CONDS,"AND");
                             if($fields1[$ii]=="") $fields1[$ii] = $table->id();
                             if($values[$ii]=="") $values[$ii] = $table->id();
                         }
                         
                         
                         for($ii=0;$ii<$this->height();$ii++){
                             $VALUES = [];
                             for($jj=0;$jj<sizeof($values);$jj++){
                                 if($this->index($values[$ii]))
                                    array_push($VALUES,$this->AV[$ii][$values[$jj]]);
                                 else array_push($VALUES,"0");
                             }
                             $additionals = $table->_gets_($fields1,$VALUES,$CONDS);
                             if($all){
                                for($iii=0;$iii<sizeof($vals);$iii++){
                                    $new_rets = $new_rets->push($ii,$new_vals[$iii],$additionals->_AV);
                                 }
                             } 
                             else {
                                if($additionals->height()==0){
                                 for($iii=0;$iii<sizeof($vals);$iii++){
                                        $new_rets = $new_rets->push($ii,$new_vals[$iii],"NULL");
                                     }
                                 }
                                 for($i=0;$i<$additionals->height();$i++){
                                     for($iii=0;$iii<sizeof($vals);$iii++){
                                         if($additionals->index($vals[$iii])){
                                             $new_rets = $new_rets->push($ii,$new_vals[$iii],$additionals->AV[$i][$vals[$iii]]);
                                         }
                                         else {
                                             $this->message = "the index of was not found on : ".$vals[$iii];
                                             $new_rets = $new_rets->push($ii,$new_vals[$iii],"NULL");
                                         }
                                     }
                                 }   
                             }
                         }
                         return $new_rets;
                     }
                 } else if((!is_array($values))&&is_array($fields1)){
                     return $this->push_($table,$vals,$new_vals,[$values],$fields1);
                 } else if(is_array($values)&&(!is_array($fields1))){
                     return $this->push_($table,$vals,$new_vals,$values,[$fields1]);
                 } else {
                     return $this->push_($table,$vals,$new_vals,[$values],[$fields1]); 
                 }
              }
            }
            else if(is_array($vals)&&(!is_array($new_vals))){
              return $this->push_($table,$vals,[$new_vals],$values,$fields1);
            }
            else if((!is_array($vals))&&is_array($new_vals)){
              return $this->push_($table,[$vals],$new_vals,$values,$fields1);
            }
            else {
              return $this->push_($table,[$vals],[$new_vals],$values,$fields1);
            }   
          }
          else {
              $this->message = "the given Table is not instance of Admin";
              return $this;
          }
      }
      // a function to feel( populate) the pipArray with the same value all around the Array
      public function push_a($name,$value){
          $theNew = $this;
          for($ii=0;$ii<$this->height();$ii++){
              $theNew = $theNew->push($ii,$name,$value);
          }
          return $theNew;
      }
      //a function to remove all data with given index
      public function pull_a($name){
          $rets = [];
          for($ii=0;$ii<$this->height();$ii++){
              $subrets = [];
              for($i=0;$i<$this->width();$i++){
                  if(!($this->_keys[$i]==$name)){
                      $subrets[$this->_keys[$i]] = $this->AV[$ii][$this->_keys[$i]];
                   }
              }
              array_push($rets,$subrets);
          }
          return pipArr($rets);
      }
      // a function to insert another PIP_Array vertically
      public function insertW($datas,$pos=NULL){
          if($datas instanceof PIP_Array){
              if(PIP_Array::sameW($this,$datas)){
                 if($pos==NULL){
                    return $this->_add_($datas);
                  } else {
                    if(!($pos<$this->heigth())){
                        $rets = [];
                        for($ii=0;$ii<$pos;$ii++){
                           array_push($rets,[]);
                           for($iii=0;$iii<$this->width();$iii++){
                               $rets[$ii][$this->_keys[$iii]] = $this->AV[$ii][$this->_keys[$iii]];
                               array_push($rets[$ii],$this->AV[$ii][$this->_keys[$iii]]);
                           }
                        }
                        for($ii=0;$ii<$datas->heigth();$ii++){
                            array_push($rets,[]);
                            for($iii=0;$iii<$this->width();$iii++){
                                $rets[$ii][$this->_keys[$iii]] = $datas->AV[$ii][$this->_keys[$iii]];
                                array_push($rets[$ii],$datas->AV[$ii][$this->_keys[$iii]]);
                            }
                        }
                        for($ii=$pos;$ii<$this->heigth();$ii++){
                            array_push($rets,[]);
                            for($iii=0;$iii<$this->width();$iii++){
                                $rets[$ii][$this->_keys[$iii]] = $this->AV[$ii][$this->_keys[$iii]];
                                array_push($rets[$ii],$this->AV[$ii][$this->_keys[$iii]]);
                            }
                        }
                        return new PIP_Array($rets);
                    } else {
                        $this->message = "a position specified is greater than the actual size of the dictionary";
                        return $this->insertW($data,NULL);
                    }
                  }   
              } else {
                  $this->message = "to insert a dictionary into another all of them must have the sane width";
                  return $this;
              }
          } else {
             $this->message = "invalid input to the insert function";
             return $this; 
          }
      }
      // a function to insert anothe PIP_Array horizontaly
      public function insertH($datas,$pos=NULL){
          if($datas instanceof PIP_Array){
              if(PIP_Array::sameH($this,$datas)){
                  if($pos==NULL){
                      $rets = NULL;
                      for($ii=0;$ii<$this->height();$ii++){
                         for($iii=0;$iii<$datas->width();$iii++){
                            $rets = $this->push($ii,$datas->_keys[$iii],$datas->AV[$ii][$datas->_keys[$iii]]);
                         }
                             
                      }
                      return $rets;
                  }
                  else {
                      if(!($pos<$this->width())){
                          $rets = [];
                          for($ii=0;$ii<$this->height();$ii++){
                              array_push($rets,[]);
                              for($iii=0;$iii<$pos;$iii++){
                                  $rets[$ii][$this->_keys[$iii]] = $this->AV[$ii][$this->_keys[$iii]];
                                  array_push($rets[$ii], $this->AV[$ii][$this->_keys[$iii]]);
                              }
                          }
                          for($ii=0;$ii<$this->height();$ii++){
                              array_push($rets,[]);
                              for($iii=0;$iii<$datas->width();$iii++){
                                  $rets[$ii][$datas->_keys[$iii]] = $datas->AV[$ii][$datas->_keys[$iii]];
                                  array_push($rets[$ii], $datas->AV[$ii][$datas->_keys[$iii]]);
                              }
                          }
                          for($ii=0;$ii<$this->height();$ii++){
                              array_push($rets,[]);
                              for($iii=$pos;$iii<$this->width();$iii++){
                                  $rets[$ii][$this->_keys[$iii]] = $this->AV[$ii][$this->_keys[$iii]];
                                  array_push($rets[$ii], $this->AV[$ii][$this->_keys[$iii]]);
                              }
                          }
                          $rets = new PIP_Array($rets);
                          return $rets;
                      } else {
                         $this->message = "a position specified is greater than the actual size of the dictionary";
                         return $this->insertH($data,NULL); 
                      }
                  }
              }
              else {
                  $this->message = "to insert a dictionary into another all of them must have the sane height";
                  return $this;
              }
              
          }
          else {
              $this->message = "invalid input to the insert function";
              return $this;
          }
      }
       // a function to compares two PIP_Array with another in width
      public static function sameW($pip1,$pip2){
          if(($pip1 instanceof PIP_Array)&&($pip2 instanceof PIP_Array)){
              $rets = true;
              if($pip1->width()!=$pip2->width())
                  $rets = false;
              for($ii=0;$ii<$pip1->width();$ii++){
                  if(!isset($pip2->_keys[$pip1->_keys[$ii]])){
                    $rets = false;
                    break;
                  }
              }
              return $rets;
          } else return false;
      }
      // a function to check if two PIP_Array are the same in Height
      public static function sameH($pip1,$pip2){
         if(($pip1 instanceof PIP_Array)&&($pip2 instanceof PIP_Array)){
             if($pip1->height()==$pip2->height())
                 return true;
             else return false;
         } else return false;
     }
       // a function to randomize list inside the object
      protected function isExist($arry,$val){
          $exist = false;
          for($ii=0;$ii<sizeof($arry);$ii++){
              if($arry[$ii]==$val){
                $exist = true;
                break;
              }
          }
          return $exist;
      }
      // a function to get a 1d array with a specified row number or a name
      public function Cols($ids = 0){
          $arr = array();
          for($ii=0;$ii<$this->size;$ii++){
              array_push($arr,$this->AV[$ii][$ids]);
          }
          return $arr;
      }
      // a public function to produce keys to be associate with cols returned with similar keys
      public function ColsKeys($name){
          $arr = [];
          for($ii=0;$ii<$this->size;$ii++){
              array_push($arr,$name);
          }
          return $arr;
      }
      // a function to return an 1d array of rows values of the given index
      public function Rows($index = 0){
          $rets = array();
          switch($index){
              case is_numeric($index):{
                  if($index<$this->size)
                      for($ii=0;$ii<$this->width();$ii++){
                          array_push($rets,$this->AV[$index][$ii]);
                      }
                  else $this->message = " the index given is out of bound: the index($index) and the size is $this->size ";
              }
              case is_string($index):{
                  $this->message = " the index must be a number to perform the operation ";
                  break;
              }
              default:{
                $this->message = "invalid index";
                break;
              }
          }
          return $rets;
      }
      // a function to return an 1d array of rows keys of the given name
      public function RowsKeys($name){
          $rets = array();
          if($this->size)
              for($ii=0;$ii<$this->width();$ii++){
                  array_push($rets,$name);
              }
          
          return $rets;
      }
      // a function to return the width of the array
      public function width(){
          return sizeof($this->_keys);
      }
      // a function to return the height of the array
      public function height(){
          return $this->size;
      }
      
      // a function to output the equivalent javascript data structure where $name is the name of that Object
      // $type is when the  output will be in the  <script> tag if 0 or not something else
      public function script($name, $type=0){
          $fields = "[";
          for($ii=0;$ii<$this->width();$ii++){
              $fields .= "'".$this->_keys[$ii]."'";
              if($ii<$this->width()-1)
                 $fields .= ",";
          }
          $fields .="]";
          
          if($type==0){
              echo "var $name = new PIP_Array($this->JV,$fields);";
          } else {
              echo "<script>
                        var $name = new PIP_Array($this->JV,$fields);
                    </script>";
          }
      }
      // a public function to dealing with html formatted strings
      public function html(){
          return new _HTML($this);
      }
      // a public function to update the pipArray with new PIP_Array given inside
      public function _add_($values = [],$fields = []){
          if($values instanceof PIP_Array){
              if($values->size==1){
                  if($this->size>0){
                     if($values->width()==$this->width()){
                            $arr = array();
                            for($ii=0;$ii<$this->size;$ii++){
                                array_push($arr,$this->AV[$ii]);
                            }
                            array_push($arr,$values->AV[0]);
                            return $this->init($arr);
                      } else {
                         $this->message = "<br> non equals keys of two dictionaries ".dataBrackets($values->_keys)." :: ".dataBrackets($this->_keys);
                         
                         echo $this->message;
                         $VALUES = array();
                         for($ii=0;$ii<sizeof($this->keys);$ii++){
                             $indx = $this->keys[$ii];
                             $vals = $this->AV[0][$indx];
                             array_push($VALUES,$vals);
                         }
                         return $this->_add_($VALUES,$values->keys);
                      } 
                  } 
                  else {
                      return $this->init($values->AV);
                  }
              } 
              else if($values->size>1){
                  $RETS = "";
                  for($ii=0;$ii<$values->size;$ii++){
                     $RETS =  $this->_add_(new PIP_Array([$values->AV[$ii]]));
                  }
                  return $RETS;
              } 
              else {
                  //$this->List();
                  return $this;
              }
          } 
          else if(is_array($values)){
              if(sizeof($values)>0){
                  if(sizeof($fields)==sizeof($values)){
                      $new_arr = array();
                      
                      for($ii=0;$ii<$this->width();$ii++){
                          array_push($new_arr,"");
                          $indx = $this->_keys[$ii];
                          $new_arr[$indx] = "";
                      }
                      
                      for($ii=0;$ii<sizeof($fields);$ii++){
                          if(!isset($values[$fields[$ii]]))
                              $values[$fields[$ii]] = $values[$ii];
                      }
                      for($v=0;$v<sizeof($fields);$v++){
                          if(contains_arr($this->keys,$fields[$v])){
                              $new_arr[$fields[$v]] = $values[$fields[$v]];
                              $new_arr[index_arr($this->keys,$fields[$v])] = $values[$fields[$v]];
                          }
                      }
                      $newP = new PIP_Array(array($new_arr));
                      return $this->_add_($newP);
                   } 
                  else if(sizeof($fields)==0){
                      if(sizeof($values)==sizeof($this->keys)){
                          return $this->_add_($values,$this->keys);
                      } else {
                          //Listi($this->keys);
                          //Listi($values);
                          $this->message = "Trying to add arrays with invalid inputs, size of provided values is not fitting in the array of the PIP_Array:: values are ".sizeof($values)." the fields of this object are ".sizeof($this->keys);
                          return $this;
                      }
                   } else {
                      $this->message = "Trying to add arrays with invalid inputs, size of values must be equal to the size of fields :: values are ".sizeof($values).":".dataBrackets($values)."and the given fields is ".sizeof($fields).":".dataBrackets($fields);
                      return $this;
                  }
              } else {
                  $this->message = "Trying to add arrays with invalid inputs, values array must be provided and not empty";
                  return $this;
              }
          } 
          else {
              $this->message = "Trying to add arrays with invalid inputs";
              return $this;
          }
      }
      // a public function to save all current updated data to the corresponding tables in the database
      public function save(){
          
      }
      // a function to return sum of element in a specified fields in assending mode from a specified index
      public function sum_asc($field,$index = 0){
          $sum = 0;
          if(!$this->index($field))
              return $sum;
          for($ii=$index;$ii<$this->height();$ii++){
              if(is_numeric($this->AV[$ii][$field]))
                $sum = $sum +  $this->AV[$ii][$field]; 
          }
          return $sum;
      }
      public function sum_dec($field,$index = -1){
          $sum = 0;
          if($index == -1)
              $index = $this->height()-1;
          else if($index==0)
              return $this->AV[0][$field];
          
          for($ii=$index;$ii>=0;$ii--){
              if(is_numeric($this->AV[$ii][$field]))
                 $sum = $sum +  $this->AV[$ii][$field];
          }
          return $sum;
      }
      public function sum($fields = 0){
          return $this->sum_asc($fields);
      }
      // a function to return the subPIP_Array of the $indexes given
      public function sub($start = 0,$end = 0){
          if(($start==0)&&($end==0)){
              return new PIP_Array([$this->AV[0]]);
          } else if(($start==0)&&($end>$this->height())){
              return $this->sub($start,$this->height());
          } else if($start>$this->height()){
              return new PIP_Array($this->AV);
          } else if(($start>0)&&($end==0)){
              return new PIP_Array([$this->AV[$start]]);
          } else {
              $rr = [];
              for($ii=$start;$ii<$end;$ii++){
                  array_push($rr,$this->AV[$ii]);
              }
              return new PIP_Array($rr);
          }
      }
      // a public function to put some json array in the form of the PIP_array
    
      public function json($str = ""){
          if(pipStr($str)->length()){
              $str = json_decode($str,true);
              if(is_array($str)){
                  if(sizeof($str)>0){
                      if(isset($str[0])){
                          return $this->init($str);
                      } else {
                          return $this->init([$str]);
                      }
                  } else return $this->init();
              } else {
                  $this->message =  json_last_error_msg();
                  return false;
              }
          } else return $this->JV;
      }
      // a function to return PIP cursor object of this elements
      public function CR(){
          return new PIP_ArrayCursor($this);
      }
}
// a function to return a a PIP_Array object
function pipArr($arr = [[]]){
    return new PIP_Array($arr);
}
// a class for PIP_Str for string values
class PIP_Str {
    private $the_string = "";
    public $str;
    public function __construct($str = ""){
        $this->the_string = $str;
        $this->str = $this->the_string;
    }
    public function str(){
        return $this->the_string;
    }
    public function init($str){
        $this->the_string = $str;
        return $this;
    }
    public function length(){
       return strlen($this->the_string);
    }
    public function sub($index,$len = 1){
         if(!is_numeric($len)) Listi($len);
        return substr($this->the_string,$index,$len);
    }
    // to check if a string is a sub of the string
    public function isSub($str){
        if(strlen(strchr($this->str(),$str))>0)
          return true;
        return false;
    }
    // comparison of the given string
    public function comp($str){
        if(is_string($str)){
            return $this->comp(pipStr($str));
        } else if($str instanceof PIP_Str){
            return (0==strcmp($this->str(),$str->str()));
        } else return false;
    }
    public function add($str, $pos = 0){
        if($pos) return pipStr($str)->add($this->str());    
        $this->the_string = $this->the_string.$str;
        return $this;
    }
    public function contains($str){
        if(is_string($str)){
            $str = pipStr($str);
            $counts = 0;
            for($ii=0;$ii<$this->length();$ii++){
                if($this->sub($ii,$str->length())==$str->str){
                    $counts++;
                }
            }
            return $counts;   
        } else if($str instanceof PIP_Array){
            for($ii=0;$ii<$str->height();$ii++){
               for($iii=0;$iii<$str->width();$iii++){
                   if($this->contains($str->AV[$ii][$iii]))
                       return true;
               }
            }
            return false;
        } else if(is_array($str)){
            return $this->contains(new PIP_Array([$str]));
        } else if(is_numeric($str)){
            return $this->contains("$str");
        }
    }
    public function insert($str,$pos = 0){
        if($pos){
            $first_sub = $this->sub(0,$pos);
            $last_sub = $this->sub($pos+1,$this->length());
            return $this->init($this->sub(0,$pos).$str.$this->sub($pos+1,$this->length()));
        } else return new $this->add($str);
    }
    public function remove($str,$from = 0){
        $the_string = $this->sub($from,$this->length());
        return $this->init(str_replace($str,"",$this->str()));
    }
    public function change($old,$new,$from = 0){
        $the_string = $this->sub($from,$this->length());
        if($this->contains($old)==0) return $this;   
        return $this->init(str_replace($old,$new,$this->str()));
    }
    public function singleLine(){
        return $this->init(str_replace("\n","",$this->str()));
    }
    public function singleText(){
        $new = str_replace(" ","",$this->str());
        $new = str_replace(chr(13),"",$new);
        return $this->init($new);
    }
    // a function to compare the string smilllity and return value in percentage
    public function compare($str){
        if(is_string($str)){
            return $this->compare(pipStr($str));
        } else if($str instanceof PIP_Str){
            $num = 0;
            $lenDifference = PIP_number($this->length() - $str->length())->absolute();
            $comparing_str = $this->nochar_repeat()->add($str->str())->nochar_repeat();
            $bigVal = $comparing_str->length();
            for($ii=0;$ii<$comparing_str->length();$ii++){
                 $num += ((1/($bigVal*pow($bigVal,
                        PIP_number($this->histogram($comparing_str->sub($ii))
                                   -$str->histogram($comparing_str->sub($ii))
                                  )->absolute())))*100);
            }
            
            return $num;
        } else return 0;
    }
    // a function to remove character repetition within a string to return a new string
    private function nochar_repeat(){
        $finalStr = pipStr();
        for($ii=0;$ii<$this->length();$ii++){
            if($finalStr->histogram($this->sub($ii))==0)
               $finalStr->add($this->sub($ii));
        }
        return $finalStr;
    }
    // a function to return the histogram value of the given char
    public function histogram($char){
        $theChar = pipStr($char);
        if($theChar->length()>1){
            return $this->histogram($theChar->sub(0));
        } else if($theChar->length()==0){
            return 0;
        }
        else return $this->contains($char);
    }
}

function pipStr($str = ""){
    return new PIP_Str($str);
}
// THE CLASS OF CHANGING GIVEN ASSOCIATED ARRAY INTO SOME JSON
class ARR_TO_JSON {
    private $ARRY;
    // the constructor that will get 2 dimmensional array as an argument and a numerical position
    function __construct($ARR,$position){
        $this->ARRY = $ARR;
        
        if(!is_numeric($position)){
            $position = sizeof($ARR)-1;
        } else if($position>=(sizeof($ARR)-1)){
             $position = sizeof($ARR)-1;
        }
        
        $keys = array_keys($this->ARRY[$position]);
        for($ii=0;$ii<sizeof($keys);$ii++){
            $THIS_STR = $keys[$ii];
            $this->$THIS_STR = $ARR[$position][$THIS_STR]; 
        } 
    }
    
    public function printi($pos = 0,$index = 0){
        if(is_numeric($pos))
            print_r($this->ARRY[$pos][$index]);
        else print_r($this->ARRY[0][$pos]);
    }
}
// the class that will keep a table with it foreign key
class tab_foreign{
    private $tab;
    private $fore;
    function __construct($TAB,$FOR){
        $this->tab = $TAB;
        $this->fore = $FOR;
    }
    
    public function GET_FORE(){
        return $this->fore;
    }
    
    public function GET_TAB(){
        return $this->tab;
    }
    
}
/* THE CLASS OF A SPECIFIC TABLE AND ALL ITS POSIBLE ACTIVITIES IN THE DATABASE */
class admin extends webApp  {
    // the name of the table
 	public $table_name = "";
    // a public variable to keep connectivity to the database
 	public $conn;
    // a public variable 
 	public $primary_key = "_id";
 	public $message = "no error found";
    public $sql;
    public $con;
    public $ENGINE = "";
    public $CHARSET = "";
    // public variables that will keep all child tables
    public $child = array();
    // public variables that will keep all parent tables
    public $parents = array();
    // a variable to keep the JS_STRUCTUCTURE of this table
    public $JS_st = array();
    // a variable to keep the backup directory
    public $backup = "";
    // a variable to keep all backupfiles;
    public $backup_files = [
        "structure"=>"structure.sql",
        "history"=>"history.sql",
        "structure_php"=>"structure.php",
        "contents"=>"contents.json",
        "index"=>"index.php"
    ];
    // a constants to keep all possible SQL JOIN
    const JOINS = ["INNER","OUTER","FULL","RIGHT","LEFT"];
    // a private array to keep all joined child tables from the previous joins in the current SQL
    private $joined_child = [];
    // a private array to keep all joined parent tables from the previous joins in the current SQL
    private $joined_parent = [];
 	function __construct($argument,$prim = "",$conns = 0){
 		$this->table_name = $argument;
 		$this->primary_key = $prim;
        $this->connecting = false;
        if($conns instanceof mysqli){
           $conns->close();
           unset($conns);
        } 
        else if(isset($conns["host"])&&isset($conns["db"])&&isset($conns["user"])&&isset($conns["password"])){
            $this->con($conns);
        } 
        else {
            
            //print_r(parent::config);
            $this->con = [
                "host"=>parent::config["HOST"],
                "db"=>$argument,
                "user"=>parent::config["USER_NAME"],
                "password"=>parent::config["PASSWORD"],
                "status"=>"waiting..."
            ];
        }
            
        $this->conn = 0;
        if($prim=="")
            $this->primary_key = $argument."_id";
        $this->sql = "SELECT * FROM `$this->table_name` ";
        $this->close();
        
        $the_primary = new JS_STRUCTURE($this->primary_key);
        $the_primary
            ->Stype("INT")
            ->Ssize("11")
            ->Sclasses($this->primary_key)
            ->SMIN("1");
        
        
        array_push($this->JS_st,$the_primary);
        $this->JS_st[$this->primary_key] = $the_primary;
 	}
    // a function to return the primary key
    public function id(){
        return $this->primary_key;
    }
    // a function to return the name of the table
    public function name(){
        return $this->table_name;
    }
    // a function to execute a query here
    function query($sql){
        $this->sql = $sql;
        $this->conn = $this->open();
        $result = mysqli_query($this->conn,$this->sql);
        if($result){
            $this->message = "the query was executed success";
            $this->close();
            return true;
        } else {
            $this->message = mysqli_error($this->conn);
            $this->close();
            return false;
        }
        
    }
    //a public function to add a joined child to the list
    public function newChild($child){
        if((!contains_arr($this->joined_child,$child))&&
           (!contains_arr($this->joined_parent,$child))&&
           (!($child==$this->table_name))){
            array_push($this->joined_child,$child);
            for($ii=0;$ii<$this->childNum();$ii++){
                $this->child[$ii]->GET_TAB()->newParent($child);
            }
        }
        return $this;
    }
    // a public function to remove a specified child on the list
    public function removeChild($child){
        remove_arr($this->joined_child,$data);
        return $this;
    }
    // a function to empty the list of child in the sql
    public function emptyChild(){
        $ii = sizeof($this->joined_child)-1;
        while(sizeof($this->joined_child)){
            $this->removeChild($this->joined_child[$ii]);
            $ii--;
        }
    }
    //a public function to add a joined parent to the list
    public function newParent($parent){
        if((!contains_arr($this->joined_parent,$parent))&&
           (!contains_arr($this->joined_child,$parent))&&
           (!($parent==$this->table_name))){
            array_push($this->joined_parent,$parent);
            for($ii=0;$ii<$this->childNum();$ii++){
                $this->child[$ii]->GET_TAB()->newChild($parent);
            }
        }      
        return $this;
    }
    // a public function to remove a specified parent on the list
    public function removeParent($parent){
        remove_arr($this->joined_parent,$data);
        return $this;
    }
    // a function to empty parent list in the sql
    public function emptyParent(){
        $ii = sizeof($this->joined_parent)-1;
        while(sizeof($this->joined_parent)){
            $this->removeParent($this->joined_parent[$ii]);
            $ii--;
        }
        return $this;  
    }
    // private function to be used while selecting datas to prevent repetitive codes
    protected function SELECT_DATAS(){
        $admin = array();
        $result = "";
        $this->conn = $this->open();
        $result = mysqli_query($this->conn,$this->sql);
       if($result){
           $admin["data_records"] = mysqli_num_rows($result);
           if($admin["data_records"]==0){
               $this->message = "Nothing stasfying condition given or the table is empty";
               $admin[0][0] = -1;
               $admin[0]["message_returneds_failed_pip_array"] = $this->message;
               $admin[0]["size_returned_failed_pip_array"] = "0 records found";
               $admin["data_records"] = 0;
           }
           $this->message = $admin["data_records"]." RECORDS found in ".$this->table_name;
	       while($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
				array_push($admin,$row);
			}
	    }
        else {
	    	$this->message = "error occured while loading data ".mysqli_error($this->conn);
            $admin[0][0] = -1;
            $admin[0]["message_returneds_failed_pip_array"] = $this->message;
            $admin[0]["size_returned_failed_pip_array"] = "0 records found";
            $admin["data_records"] = 0;
	   }
      $this->close();
      return $admin;
    }
    public function con($con){
        if(isset($con["host"])&&isset($con["db"])&&isset($con["user"])&&isset($con["password"])&&isset($con["backup"])){
            $this->con = $con;
            if(is_dir($con["backup"])){
                $this->backup = $con["backup"]."/".$this->table_name."/";
                if(!is_dir($this->backup)){
                    mkdir($this->backup);
                }
                $this->backup_files["structure"] = $this->backup.$this->backup_files["structure"];
                $this->backup_files["history"] = $this->backup.$this->backup_files["history"];
                $this->backup_files["structure_php"] = $this->backup.$this->backup_files["structure_php"];
                $this->backup_files["contents"] = $this->backup.$this->backup_files["contents"];
                $this->backup_files["index"] = $this->backup.$this->backup_files["index"];
                
                $struct = fopen($this->backup_files["structure"],'w');
                $history = fopen($this->backup_files["history"],'w');
                $struct_php = fopen($this->backup_files["structure_php"],'w');
                $contents = fopen($this->backup_files["contents"],'w');
                $index = fopen($this->backup_files["index"],'w');
                    
                fclose($struct);
                fclose($history);
                fclose($struct_php);
                fclose($contents);
                fclose($index);
                
                //$all = dir(getcwd());
                //echo "<pre>".print_r($all,1)."</pre>";
            }
        }
        return $this;
    }
    public function db($db){
        $this->con["db"] = $db;
        return $this;
    }
    // a function to search in the table records
    public function search($keyword,$where,$order="",$start=-1,$lenght=0){
        $this->sql = "SELECT * FROM `".$this->table_name."`";
        for($ff=0;$ff<sizeof($where);$ff++){
            if($ff>0)
                $this->sql .= " OR ";
            else $this->sql .= "  WHERE ";
            $this->sql .= " `".$where[$ff]."` LIKE '%$keyword%'";
        }
        $this->ORDERBY($order,$start,$lenght);
        return new PIP_Array($this->SELECT_DATAS());
    }
    // a private function to compose the query to search from multiple keys
    private function searchML($keywords,$where){
        $this->sql = "SELECT * FROM `".$this->table_name."`";
        $exist_comparison = new PIP_Array(array());
        $init = 0;
        for($ff=0;$ff<sizeof($keywords);$ff++){
            for($fff=0;$fff<sizeof($where);$fff++){
                if($init==0){
                   $this->sql .= " WHERE ";
                   $new_comp = [
                    "keywords"=>$keywords[$ff],
                    "field"=>$where[$fff]
                   ];
                   $exist_comparison = $exist_comparison->init([$new_comp]);
                   $this->sql .= "`".$where[$fff]."` LIKE '%".$keywords[$ff]."%'";
                } else {
                   $pos_keywords = $exist_comparison->indexOf($keywords[$ff],"keywords");
                   
                   if(!$exist_comparison->isindexOf($where[$fff],$pos_keywords,"field")){
                       $exist_comparison->_add_([$keywords[$ff],$where[$fff]],["keywords","field"]);  
                       $this->sql .= " OR ";
                       $this->sql .= "`".$where[$fff]."` LIKE '%".$keywords[$ff]."%'";
                   }
                   //echo "<br>".$exist_comparison->message."<br>";
                }
                $init++;
            }
        }
        //$exist_comparison->List();
    }
    // a private function to add limits and orders on the current query
    private function ORDERBY($order="",$start=-1,$lenght=0){
        if(!($order==""))
            $this->sql .= " ORDER BY `".$this->table_name."`.`".$order."`";
        else $this->sql .= " ORDER BY `".$this->table_name."`.`".$this->primary_key."`";
        if($start>-1)
            $this->sql .= " LIMIT $start , $lenght ";
    }
    // a function to search in the table with multiple keys
    public function searchM($keywords,$where,$order="",$start=-1,$lenght=0){
        if(!is_array($keywords)) $keywords = [$keywords];
        $this->searchML($keywords,$where);
        $this->ORDERBY($order="",$start=-1,$lenght=0);
        return new PIP_Array($this->SELECT_DATAS());
    }
    // a function to search with the and clause on the specified fields and maching eith specified values
    public function searchMW($keywords,$where,$fields, $values,$order="",$start=-1,$lenght=0){
        $this->searchML($keywords,$where);
        if(is_array($fields)){
            for($ii=0;$ii<sizeof($fields);$ii++){
                if(isset($fields[$ii])&&isset($values[$ii]))
                    $this->sql .= " AND `".$fields[$ii]."` = '".$values[$ii]."' ";
                else break;
            }
        } else {
            $this->sql .= " AND `$fields` = '$values' "; 
        }
        $this->ORDERBY($order="",$start=-1,$lenght=0);
        return new PIP_Array($this->SELECT_DATAS());
    }
    public function searchI($id,$where,$order="",$start=-1,$lenght=0){
        $keys = "(".$id.")";
        return $this->search($keys,$where,$order,$start,$lenght);
    }
    // function to edit datas of a single row of field named $field with specified $id  in the given table where $VALUE is a new data to replace the current one $id is the id of the row to be edited and $mode is the HTTP Method used WHERE 0 is POST , 1 is GET and 2 is direct datas
 	public function edit($field,$VALUE,$id,$mode = 2){
       $this->conn = $this->_connection_();
 	   $ids = 0;
 	   $fields = $this->primary_key;
 	   $VALUES = 0;
 	   switch ($mode) {
 	   	case 0:{
            $ids = $id;
            if(isset($_POST[$id])){
	 	   	  $ids = $_POST[$id];
	 	   }
	 	   $fields = $field;
	 	   if(isset($_POST[$VALUE])){
	 	   	  $VALUES = $_POST[$VALUE];
              $VALUES = stripslashes($VALUES); 
              $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
	 	   }
 	   		break;
        }
 	   	   
 	   	case 1:{
            $ids = $id;
            if(isset($_GET[$id])){
	 	   	  $ids = $_GET[$id];
	 	   }
	 	   	  $fields = $field;
	 	   if(isset($_GET[$VALUE])){
	 	   	  $VALUES = $_GET[$VALUE];
              $VALUES = stripslashes($VALUES); 
              $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
	 	   }
 	   		break;
        }
 	   	case 2:{
           $ids = $id;
	 	   	$fields = $field;
	 	   	$VALUES = $VALUE;
            $VALUES = stripslashes($VALUES); 
            $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
 	   		break; 
        }
	 	   	
 	   	default:{
           $ids = $id;
	 	   	$fields = $field;
	 	   	$VALUES = $VALUE;
            $VALUES = stripslashes($VALUES); 
            $VALUES = mysqli_real_escape_string($this->conn,$VALUES);
 	   		break; 
          }	
 	   }
 	   $statatus = false;
       $this->sql = "UPDATE `$this->table_name` SET `$fields` = '$VALUES' WHERE `$this->table_name`.`$this->primary_key` = '$ids'";
       
       $result = mysqli_query($this->conn,$this->sql);
       if($result){
			$this->message = " edit has been done well ".'  ';
			$statatus = true;
	   }
	   else {
	   	    $this->message = " failed to edit ".mysqli_error($this->conn).'  ';
			$statatus = false;
	   }
       $this->conn->close();
       $this->conn = 0;
	   return $statatus;
 	}
    // a function to edit datas of a single row of multiple fields named in $field array with specified $id in the given table where $VALUE is an array of new data to be saved and the rest parameters are the same as edit();
    public function editM($field,$VALUE,$id,$mode = 2){
        $this->conn = $this->_connection_();
        $ids = 0;
        $VALUES = array();
        $fields = array();
        $statatus = false;
        if(sizeof($VALUE)==sizeof($field)){
        switch($mode){
            case 0:{
                for($kk=0;$kk<sizeof($field);$kk++){
                    if(isset($_POST[$VALUE[$kk]])){
                        array_push($fields,$field[$kk]);
                        $V = $_POST[$VALUE[$kk]];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                    }
                }
                if(isset($_POST[$id])){
                    $ids = $_POST[$id];
                } else $ids = $id;
                break;
            }
            case 1:{
                for($kk=0;$kk<sizeof($field);$kk++){
                    if(isset($_GET[$VALUE[$kk]])){
                        array_push($fields,$field[$kk]);
                        $V = $_GET[$VALUE[$kk]];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                    }
                }
                if(isset($_GET[$id])){
                    $ids = $_GET[$id];
                } else $ids = $id;
                break;
            }
            case 2:{
                for($kk=0;$kk<sizeof($field);$kk++){
                        array_push($fields,$field[$kk]);
                        $V = $VALUE[$kk];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                }
                $ids = $id;
                
                break;
            }
            default:{
                for($kk=0;$kk<sizeof($field);$kk++){
                        array_push($fields,$field[$kk]);
                        $V = $VALUE[$kk];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                }
                    $ids = $id;
                break;
            }
        }
        $this->sql = "UPDATE `$this->table_name` SET";
        for($tt=0;$tt<sizeof($fields);$tt++){
            if($tt<(sizeof($fields)-1)){
                $this->sql = $this->sql." `".$fields[$tt]."` = '".$VALUES[$tt]."' ,";
            } else $this->sql = $this->sql." `".$fields[$tt]."` = '".$VALUES[$tt]."' WHERE `$this->table_name`.`$this->primary_key` = '$ids'";
        }
        
        $result = mysqli_query($this->conn,$this->sql);
        if($result){
           $rowsAffected = mysqli_affected_rows($this->conn);
            if($rowsAffected){
                $this->message = $rowsAffected." row affected ";
			    $statatus = true;
            } else {
                $this->message = " there is nothing affected with this query may be there is no row which is satisfying the conditions or there no change done => ".mysqli_error($this->conn);
			    $statatus = false;
            } 
        } else {
            $this->message = " failed to edit ".mysqli_error($this->conn).'  ';
			$statatus = false;
        }
        
        
            
     } 
        else {
            $this->message = " number of fields and number of new values must be equal ";
            $statatus = false;
        }
        $this->conn->close();
        $this->conn = 0;
        return $statatus;
        
        
    }
    // a function with the same features as the above editM but with multiple row with conditions and fields that satisfies that condition with boolean operators specified
    public function editM_($field,$VALUE,$condition,$field_with_condition,$OPERATORS,$mode){
        $VALUES = array();
        $fields = array();
        $statatus = false;
        $this->conn = $this->open();
        if(sizeof($VALUE)==sizeof($field)){
        switch($mode){
            case 0:{
                for($kk=0;$kk<sizeof($field);$kk++){
                    if(isset($_POST[$VALUE[$kk]])){
                        array_push($fields,$field[$kk]);
                        $V = $_POST[$VALUE[$kk]];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                    }
                }
                break;
            }
            case 1:{
                for($kk=0;$kk<sizeof($field);$kk++){
                    if(isset($_GET[$VALUE[$kk]])){
                        array_push($fields,$field[$kk]);
                        $V = $_GET[$VALUE[$kk]];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                    }
                }
                break;
            }
            case 2:{
                for($kk=0;$kk<sizeof($field);$kk++){
                        array_push($fields,$field[$kk]);
                        $V = $VALUE[$kk];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                }
                
                break;
            }
            default:{
                for($kk=0;$kk<sizeof($field);$kk++){
                        array_push($fields,$field[$kk]);
                        $V = $VALUE[$kk];
                        $V = stripslashes($V); 
                        $V = mysqli_real_escape_string($this->conn,$V);
                        array_push($VALUES,$V);
                }
                break;
            }
        }
        $this->close();
        $this->conn = 0;
        $this->sql = "UPDATE `$this->table_name` SET";
        for($tt=0;$tt<sizeof($fields);$tt++){
            if($tt<(sizeof($fields)-1)){
                $this->sql = $this->sql." `".$fields[$tt]."` = '".$VALUES[$tt]."' ,";
            } else $this->sql = $this->sql." `".$fields[$tt]."` = '".$VALUES[$tt]."'";
        }
        
        for($tt=0;$tt<sizeof($condition);$tt++){
            if(!$tt)
              $this->sql = $this->sql." WHERE `".$field_with_condition[$tt]."` = '".$condition[$tt]."' ";
            else{
              $this->sql = $this->sql.$OPERATORS[$tt-1]."  `".$field_with_condition[$tt]."` = '".$condition[$tt]."' ";
            } 
        }
            
        $this->conn = $this->_connection_();
        $result = mysqli_query($this->conn,$this->sql);
        if($result){
           $rowsAffected = mysqli_affected_rows($this->conn);
            if($rowsAffected){
                $this->message = $rowsAffected." row affected ";
			    $statatus = true;
            } else {
                $this->message = " there is nothing affected with this query may be there is no row which is satisfying the conditions or there no change done => ".mysqli_error($this->conn);
			    $statatus = false;
            } 
        } else {
            $this->message = " failed to edit ".mysqli_error($this->conn).'  ';
			$statatus = false;
        }
        
        
            
     } else {
            $this->message = " number of fields and number of new values must be equal ";
            $statatus = false;
        }
        $this->conn->close();
        $this->conn = 0;
        return $statatus;
        
        
    }
    // function to edit datas of multiple rows of field named $field with specified $condition with matchind $field_with_condition  in the given table
    public function edit_($field,$VALUE,$condition,$field_with_condition,$mode){
        $conditions = 0;
        $VALUES = 0;
        $this->conn = $this->_connection_();
        switch($mode){
                case 0:{
                    if(isset($_POST[$VALUE])){
                        $VALUES = $_POST[$VALUE];
                    }
                    
                    if(isset($_POST[$condition])){
                       $conditions = $_POST[$condition];
                    }
                    break;
                }
                case 1:{
                    if(isset($_GET[$VALUE])){
                        $VALUES = $_GET[$VALUE];
                    }
                    if(isset($_GET[$condition])){
                       $conditions = $_GET[$condition];
                    }
                    break;
                }
                case 2:{
                    $VALUES = $VALUE;
                    $conditions = $condition;
                    break;
                }
                default:{
                    $VALUES = $VALUE;
                    $conditions = $condition;
                    break;
                }
        }
        $statatus = false;
        $this->sql = "UPDATE `$this->table_name` SET `$field` = '$VALUES' WHERE `$this->table_name`.`$field_with_condition` = '$conditions'";
        $result = mysqli_query($this->conn,$this->sql);
        if($result){
           $rowsAffected = mysqli_affected_rows($this->conn);
            if($rowsAffected){
                $this->message = $rowsAffected." row affected ";
			    $statatus = true;
            } else {
                $this->message = " there is nothing affected with this query may be there is no row which is satisfying the conditions or there no change done => ".mysqli_error($this->conn);
			    $statatus = true;
            } 
        } else {
            $this->message = " failed to edit ".mysqli_error($this->conn).'  ';
			$statatus = false;
        }
        $this->conn->close();
        $this->conn = 0;
        return $statatus;
        
    }
    // a public function to edit a row of a given condition instead of deleting and inserting again
    public function editE(){
        
    }
    //  a public function to delete all childs records of the item given 
    private function deleteCh($index,$id){
        $this->sql = "";
        $this_child_items;
        if(is_array($id)){
            $fores = [];
            for($iii=0;$iii<sizeof($id);$iii++){
                array_push($fores,$this->child[$index]->GET_FORE());
               if($iii==0){
                      $this->sql = "DELETE FROM 
                      `".$this->child[$index]->GET_TAB()->table_name."` WHERE 
                      `".$this->child[$index]->GET_TAB()->table_name."`.`".$this->child[$index]->GET_FORE()."`
                      = '".$id[$iii]."' ";
               } else {
                   $this->sql = $this->sql." OR 
                   `".$this->child[$index]->GET_TAB()->table_name."`.`".$this->child[$index]->GET_FORE()."`
                      = '".$id[$iii]."' ";
               }
            }
            $this_child_items = $this->child[$index]->GET_TAB()->_gets_($fores,$id);
        }
        
        else {
            $this->sql = "DELETE FROM 
                      `".$this->child[$index]->GET_TAB()->table_name."` WHERE 
                      `".$this->child[$index]->GET_TAB()->table_name."`.`".$this->child[$index]->GET_FORE()."`
                      = '".$id."' ";
            $this_child_items = $this->child[$index]->GET_TAB()->_gets_([$this->child[$index]->GET_FORE()],[$id]);
        }
        
        $this->conn = $this->_connection_();
        $statatus = false;
        if(mysqli_query($this->conn,$this->sql)){
            $statatus = true;
            $this->message = "Item removed success";
            $allIds = [];
            for($ii=0;$ii<$this_child_items->size;$ii++)
               array_push($allIds,$this_child_items->AV[$ii][0]);
            if($this_child_items->size>0)
                for($ii=0;$ii<$this->child[$index]->GET_TAB()->childNum();$ii++){
                    if(!$this->child[$index]->GET_TAB()->deleteCh($ii,$allIds)){
                        $statatus = false;
                        $this->child[$index]->GET_TAB()->child[$ii]->GET_TAB()->message;
                        break;
                    }
                }  
        }
        
        else {
            $statatus = true;
            $this->message = " failed to delete :".mysqli_error($this->conn);
        }
        $this->conn->close();
        $this->conn = 0;
	    return $statatus;
    }
    // function to edit datas of multiple rows of multiple fields within $field array and new datas in $VALUE array, 
 	public function delete($field,$id){
 	   $statatus = false;
 	   $values = 0;
       $this->conn = $this->_connection_();
       if(is_numeric($id))
           $values = $id;
       else if(isset($_POST[$id]))
           $values = $_POST[$id];
       else if(isset($_GET[$id]))
           $values = $_GET[$id];
       else $values = $id;
        $values = mysqli_real_escape_string($this->conn,stripslashes($values));
        $field = mysqli_real_escape_string($this->conn,stripslashes($field));
        $this_item = $this->_gets_([$field],[$values]);
        $this->conn = $this->_connection_();
        $this_ids = [];
        if($this_item->size>0){
            for($i=0;$i<$this_item->size;$i++){
                array_push($this_ids,$this_item->AV[$i][0]);
                if($i==0)
                    $this->sql = "DELETE FROM `$this->table_name` WHERE `$this->table_name`.`$this->primary_key` = '".$this_item->AV[$i][0]."' ";
                else 
                    $this->sql = $this->sql." OR `$this->table_name`.`$this->primary_key` = '".$this_item->AV[$i][0]."' ";
            }
            $result = mysqli_query($this->conn,$this->sql);
            if($result){
                $statatus = true;
                for($ii=0;$ii<$this->childNum();$ii++){
                    if(!$this->deleteCh($ii,$this_ids)){
                        $statatus = false;
                        $this->message = $this->child[$ii]->GET_TAB()->message;
                        break;
                    }
                }
            } else {
                $this->message = " failed to delete :".mysqli_error($this->conn);
                $statatus = false;
            }
        } 
        else {
            $this->message = "Internal System error: Records with ".$field." of (".$values.") was not found in ".$this->table_name;
            $statatus = false;
        }
       if($this->conn instanceof mysqli)
           $this->conn->close();
       $this->conn = 0;
	   return $statatus;
 	}
 	public function delete_($id,$fields=""){
        if(!($fields=="")) return $this->delete($fields,$id);
        else return $this->delete($this->primary_key,$id);
 	}
    // function to delete a record with multiple conditions  AND
    public function deleteM($fields,$ids){
        $statatus = false;
 	    $values = 0;
        $VALUES = array();
        $this->sql = "DELETE FROM `$this->table_name` WHERE";
        $CONDS = [];
        for($aa=0;$aa<sizeof($fields);$aa++){
            array_push($CONDS,"AND");
        }
        $this_items = $this->_gets_($fields,$ids,$CONDS);
        $this_ids = [];
        for($aa=0;$aa<$this_items->size;$aa++)
            array_push($this_ids,$this_items->AV[$aa][0]);
        $this->conn = $this->_connection_();
        if(sizeof($ids)==sizeof($fields)){
            for($ii=0;$ii<sizeof($ids);$ii++){
                if(isset($_POST[$ids[$ii]])){
                    $VALUES[$ii] = mysqli_real_escape_string($this->conn,stripslashes($_POST[$ids[$ii]]));
                } else if(isset($_GET[$ids[$ii]])){
                    $VALUES[$ii] = mysqli_real_escape_string($this->conn,stripslashes($_GET[$ids[$ii]]));
                } else {
                    $VALUES[$ii] = mysqli_real_escape_string($this->conn,stripslashes($ids[$ii]));
                }
                if($ii){
                    $this->sql = $this->sql." AND `$this->table_name`.`".$fields[$ii]."` = '".$VALUES[$ii]."'";  
                } else $this->sql = $this->sql." `$this->table_name`.`".$fields[$ii]."` = '".$VALUES[$ii]."'";     
            }
            if(mysqli_query($this->conn,$this->sql)){
                $this->message = "the item has been removed successfully";
                $statatus =  true;
                for($aa=0;$aa<$this->childNum();$aa++)
                    if(!$this->deleteCh($aa,$this_ids)){
                       $statatus = false;
                       $this->message = $this->child[$aa]->GET_TAB()->message;
                       break;
                    }
            } else {
                $this->message = "failed to remove an item.: ".mysqli_error($this->conn);
                $statatus = false;
            }
        } else {
            $this->message = "Number of fields and number of values must be equal";
            $statatus = false;
        }
        $this->conn->close();
        $this->conn = 0;
        return $statatus;
    }
    // a public function that will delete all the items in the table and all children and grand children
    public function _add_($fields,$values = [],$date_field=""){
        if($fields instanceof PIP_Array){
            if($fields->height()==0){
                return false;
            } else {
                //#########################################
                #########################################
                ###### to add real codes later ############
                ##########################################
                ##########################################
                //
                $this->sql = "INSERT INTO `".$this->table_name."` (`".$this->primary_key."`";
                for($ii=0;$ii<$fields->width();$ii++){
                    $this->sql .= ",`".$fields->_keys[$ii]."`"; 
                }
                $this->sql .= ") VALUES";
                for($ii=0;$ii<$fields->height();$ii++){
                    $this->sql .= "( NULL, ";
                    for($i=0;$i<$fields->width();$i++){
                        $this->sql .= "'".pipStr($fields->AV[$ii][$fields->_keys[$i]])->remove("rn")->str()."'";
                        if($i<$fields->width()-1)
                            $this->sql .= ",";
                    } 
                    $this->sql .= ")";
                    if($ii<$fields->height()-1)
                        $this->sql .= ",";
                }
                $this->conn = $this->open();
                $result = mysqli_query($this->conn,$this->sql);
                $statatus = 0;
                if($result){
                    $this->message = "data added seccessfuly ";
                    $statatus = mysqli_insert_id($this->conn);
                }
                else {
                    $this->message = "error occured while adding data ".mysqli_error($this->conn);
                    $statatus = 0;
                }
                $this->conn->close();
                $this->conn = 0;
                return $statatus;
            }
        } 
        else if(sizeof($fields)>=sizeof($values)){
            $this->conn = $this->_connection_();
            $fieldsX = array();
            $valuesX = array();
            $directX = array();
            for($ii=0;$ii<sizeof($fields);$ii++){
                $directX[$ii] = $ii;
                if(isset($_POST[$fields[$ii]])&&($fields[$ii]!=$values[$ii])){
                    $fieldsX[$ii] = mysqli_real_escape_string($this->conn,stripslashes($_POST[$fields[$ii]]));
                } else if(isset($_GET[$fields[$ii]])&&($fields[$ii]!=$values[$ii])){
                    $fieldsX[$ii] = mysqli_real_escape_string($this->conn,stripslashes($_GET[$fields[$ii]]));
                } else {
                    $fieldsX[$ii] = mysqli_real_escape_string($this->conn,stripslashes($fields[$ii]));
                }
                
                if(sizeof($values)&&isset($values[$ii])&&($values[$ii]!="")){
                        if(isset($_POST[$values[$ii]])){
                             $valuesX[$ii] = mysqli_real_escape_string($this->conn,stripslashes($_POST[$values[$ii]]));
                        } else if(isset($_GET[$values[$ii]])){
                             $valuesX[$ii] = mysqli_real_escape_string($this->conn,stripslashes($_GET[$values[$ii]]));
                        } else {
                             $valuesX[$ii] = mysqli_real_escape_string($this->conn,stripslashes($values[$ii]));
                        }
                } 
                else {
                    $valuesX[$ii] = $fieldsX[$ii];
                }
            }
            
            $this->conn->close();
            $this->conn = 0;
            if($date_field==""){
                return $this->add_($fieldsX,$valuesX,$directX);
                
            } else {
                return $this->add_current_time_($fieldsX,$valuesX,$directX,$date_field);
            }
        }
        else {
            $this->message = "Number of fields and number of value must equal";
            return 0;
        } 
    }
    public function add_($fields,$values,$direct = []){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
       $length_ = sizeof($direct); 
       $this->conn = $this->_connection_();
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){
           $includedInDirectValue = false;
           for($ii=0;$ii<$length_;$ii++){
               if($direct[$ii]==$key)
                 $includedInDirectValue = true;  
           }
           if($includedInDirectValue){
              $temp = $values[$key];
           } else if(isset($_POST[$values[$key]])) $temp = $_POST[$values[$key]];
                  else if(isset($_GET[$values[$key]])) $temp = $_GET[$values[$key]];
          $temp = stripslashes($temp); 
          $temp = mysqli_real_escape_string($this->conn,$temp);
          $values_s .= ",'".$temp."'";
          $includedInDirectValue = false;
 	   }
 	   $this->sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s) 
 	                   VALUES (NULL $values_s)";
 	   $result = mysqli_query($this->conn,$this->sql);
 	   if($result){
			$this->message = "data added seccessfuly ";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->message = "error occured while adding data ".mysqli_error($this->conn);
			$statatus = 0;
		}
        $this->conn->close();
        $this->conn = 0;
		return $statatus;
 	}
    public function add_current_time_($fields,$values,$direct,$date_field){
       $this->conn = $this->_connection_();
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
       $length_ = sizeof($direct); 
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){
           $includedInDirectValue = false;
           for($ii=0;$ii<$length_;$ii++){
               if($direct[$ii]==$key)
                 $includedInDirectValue = true;  
           }
           if($includedInDirectValue){
              $temp = $values[$key];
           } else if(isset($_POST[$values[$key]])) $temp = $_POST[$values[$key]];
                  else if(isset($_GET[$values[$key]])) $temp = $_GET[$values[$key]];
          $temp = stripslashes($temp); 
          $temp = mysqli_real_escape_string($this->conn,$temp);
          $values_s .= ",'".$temp."'";
          $includedInDirectValue = false;
 	   }
 	   $this->sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s,`$date_field`) 
 	                   VALUES (NULL $values_s,CURRENT_TIMESTAMP)";
 	   $result = mysqli_query($this->conn,$this->sql);
 	   if($result){
			$this->message = "data added seccessfuly success";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->message = "error occured while adding data ".mysqli_error($this->conn);
			$statatus = 0;
		}
        $this->conn->close();
        $this->conn = 0;
		return $statatus;
 	}
    public function add($fields,$values){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
       $this->conn = $this->_connection_();
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){   
 	   	if(isset($_POST[$values[$key]]))
 	   	   $temp = $_POST[$values[$key]];
 	   	else if(isset($_GET[$values[$key]]))
 	   	   $temp = $_GET_POST[$values[$key]];
        else $temp = $key;
 	   	   $temp = stripslashes($temp); 
		   $temp = mysqli_real_escape_string($this->conn,$temp);
           $values_s .=",'".$temp."'";
 	   }
 	   $this->sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s) 
 	                   VALUES (NULL $values_s)";
 	   $result = mysqli_query($this->conn,$this->sql);
 	   if($result){
			$this->message = "data added seccessfuly success";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->message = "error occured while adding data ".mysqli_error($this->conn);
			$statatus = 0;
		}
        $this->conn->close();
        $this->conn = 0;
		return $statatus;
 	}
 	public function add_current_time($fields,$values,$date_field){
 	   $statatus = 0;
 	   $fields_s = "";
 	   $values_s = "";
 	   $length = sizeof($fields);
        $this->conn = $this->_connection_();
 	   for($key = 0;$key<$length;$key++){
            $fields_s .=",`".$fields[$key]."`";
 	   }
 	   for($key = 0;$key<$length;$key++){
 	   	   $temp = "xxxx";
 	   	   if(isset($_POST[$values[$key]]))
 	   	   	   $temp = $_POST[$values[$key]];
 	   	   else if(isset($_GET[$values[$key]]))
 	   	   	   $temp = $_GET[$values[$key]];
 	   	   $temp = stripslashes($temp); 
		   $temp = mysqli_real_escape_string($this->conn,$temp);
           $values_s .=",'".$temp."'";
 	   }
 	   $this->sql = "INSERT INTO 
 	                  `$this->table_name` (`$this->primary_key` $fields_s,`$date_field`) 
 	                   VALUES (NULL $values_s,CURRENT_TIMESTAMP)";
 	   $result = mysqli_query($this->conn,$this->sql);
 	   if($result){
			$this->message = "data added seccessfuly success";
			$statatus = mysqli_insert_id($this->conn);
		}
		else {
			$this->message = "error occured while adding data ".mysqli_error($this->conn);
			$statatus = 0;
		}
        $this->conn->close();
        $this->conn = 0;
		return $statatus;
 	}
    // a function to fetch latest records in the table
    public function last($field = "",$number = 1){
        if(is_numeric($field))
            return $this->last("",$field );
        if(pipStr($field)->length()==0){
            $field = "*";
        }
        $this->sql = 
            "SELECT $field FROM `".$this->table_name."` ORDER BY `".$this->table_name."`.`".$this->primary_key."` DESC LIMIT 0,$number";
        return new PIP_Array($this->SELECT_DATAS());
    }
    // a function to fecth first records in the table
    public function first($field = "", $number = 1){
        if(is_numeric($field))
            return $this->first("",$field);
        if(pipStr($field)->length()==0){
            $field = "*";
        }
        $this->sql = 
            "SELECT $field FROM `".$this->table_name."` ORDER BY `".$this->table_name."`.`".$this->primary_key."` ASC LIMIT 0,$number";
        return new PIP_Array($this->SELECT_DATAS());
    }
    // function that will fetch all datas from the table with comparison operator of =
 	public function _gets_($fields=[],$values=[],$conds=[],$order="",$start=-1,$lenght=0,$ops = "="){ 
        $opsx = "=";
        if((substr($this->sql,0,6)!="SELECT")||(pipStr($this->sql)->isSub("COUNT(".$this->primary_key.")")))
            $this->sql = "SELECT * FROM `$this->table_name` ";
        if(!is_array($fields)){
            if(is_array($ops)){
               if(isset($ops[0]))
                 if(self::is_operator($ops[0]))
                    $opsx = $ops[0];   
            } else if(self::is_operator($ops)) $opsx = $ops;
                
            if(!is_array($values)){
               $this->sql = "SELECT * FROM `$this->table_name` WHERE `$this->table_name`.`$fields` $opsx '$values'";
            } else {
               $this->sql = "SELECT * FROM `$this->table_name` WHERE `$this->table_name`.`$this->primary_key` $opsx '$fields'"; 
            }
            return new PIP_Array($this->SELECT_DATAS());
        }
        else {
        if(!is_array($values)){
            $new_vals = [];
            for($ii=0;$ii<sizeof($fields);$ii++){
                array_push($new_vals,$values);
            }
            return $this->_gets_($fields,$new_vals,$conds,$order,$start,$lenght,$ops);
        }
        if(sizeof($fields)){
            if(sizeof($fields)==sizeof($values)){
                $exist_comparison = new PIP_Array(array());
                for($ii=0;$ii<sizeof($fields);$ii++){
                    if($ii==0){
                        $new_comp = [
                            "value"=>$values[$ii],
                            "field"=>$fields[$ii]
                        ];
                        $exist_comparison = $exist_comparison->init([$new_comp]);
                    } else {
                        if(!$exist_comparison->isindexOf($fields[$ii],
                                                         $exist_comparison->indexOf($values[$ii],"value"),
                                                         "field")){
                            $exist_comparison->_add_([$values[$ii],$fields[$ii]],
                                                     ["value","field"]);
                            
                        }
                    }
                }
                $values = $exist_comparison->Cols("value");
                $fields = $exist_comparison->Cols("field"); 
                if(sizeof($fields)>1){
                    if((sizeof($conds)<=((sizeof($fields)-1)))){
                        if(($order=="")&&($start==-1)&&($lenght==0)){
                           return $this->_gets__($fields,$values,$conds,$ops); 
                        } else if(($order!="")&&($start==-1)&&($lenght==0)){
                            return $this->_gets__ORDER($fields,$values,$conds,$order,$ops);
                        } else if(($order=="")&&(!($start<0))&&(!($lenght<1))){
                            return $this->_gets__LIMIT($fields,$values,$conds,$start,$lenght,$ops);
                        } else if(($order!="")&&(!($start<0))&&(!($lenght<1))){
                            return $this->_gets__ORDER_LIMIT($fields,$values,$conds,$order,$start,$lenght,$ops);
                        } else {
                            $this->message = "invalid input";
                            return $this->_gets__ORDER($fields,$values,$conds,$this->primary_key,$ops);
                        }
                    }
                    else if(sizeof($conds)==sizeof($fields)){
                        $CONDS = array();
                        for($ii=0;$ii<(sizeof($conds)-1);$ii++){
                            array_push($CONDS,$conds[$ii]);
                        }
                        $rets = $this->_gets_($fields,$values,$CONDS,$order,$start,$lenght,$ops);
                        $rets->message = "for boolean operators that are equal to fields specified, the last condition will be ommited";
                        return $rets;
                    }
                    else {
                       $CONDS = array();
                       for($ii=1;$ii<sizeof($fields);$ii++){
                           array_push($CONDS,"OR");
                       }
                       $rets = $this->_gets_($fields,$values,$CONDS,$order,$start,$lenght,$ops);
                       $rets->message = "For field condition you have to specify all boolean operators in this case the OR operator will be used everywhere instead";
                       return $rets;
                    }
                } 
                else {
                    if($start==-1)
                        return $this->_gets__($fields,$values,$conds,$ops);
                    else {
                        if($order==""){
                            $order = $this->primary_key;
                        }
                        $this->sql = "SELECT * FROM `$this->table_name` WHERE `$this->table_name`.`".$fields[0]."` $opsx '".$values[0]."' ORDER BY $order DESC LIMIT $start , $lenght"; 
                        return pipArr($this->SELECT_DATAS());
                    }
                }
            }
            else if(sizeof($values)==0){
                $new_values = array();
                $new_conds = array();        
                $fields = new PIP_Array([$fields]);
                $fields = $fields->filterthis_distinct();
                for($ii=0;$ii<$fields->width();$ii++){
                    array_push($new_values,$this->primary_key);
                    array_push($new_conds,"OR");
                }
                if(is_array($conds)){
                    if(((sizeof($new_values)-sizeof($conds))==1)||((sizeof($new_values)-sizeof($conds))==0))
                        $new_conds = $conds;
                    else if(sizeof($conds)>0){
                        $new_conds = [];
                        for($ii=0;$ii<sizeof($new_values);$ii++){
                            if(isset($conds[$ii]))
                                array_push($new_conds,$conds[$ii]);
                            else array_push($new_conds,$conds[sizeof($conds)-1]);
                        }
                    }
                }   
                if($fields->height())
                    return $this->_gets_($new_values,$fields->AV_[0],$new_conds,$order,$start,$lenght,$ops);
                else return pipArr([]);
            } 
            else{
               $this->message = "Size of Field and value must be equal";
               return new PIP_Array(array());
            } 
        } 
        else {
            if(($order=="")&&($start==-1)&&($lenght==0)){
                return new PIP_Array($this->SELECT_DATAS()); 
            } else if(($order!="")&&($start==-1)&&($lenght==0)){
                return $this->_gets_ORDER($order);
            } else if(($order=="")&&(!($start<-1))&&(!($lenght<1))){
                return $this->_gets_LIMIT($start,$lenght);
            } else if(($order!="")&&(!($start<-1))&&(!($lenght<1))){
                return $this->_gets_ORDER_LIMIT($order,$start,$lenght);
            } else {
                $this->message = "invalid input";
                return new PIP_Array($this->SELECT_DATAS());
            }
        }
        }
 	}
    //a function to return the pipArray with the corresponding values
    public function with_ids($values,$start = -1,$length = 0){
        if(!is_array($values))
            return $this->with_ids([$values]);
        $rests = self::with_id_conds($values);
        return $this->_gets_($rests["values"],[],$rests["conds"],"",$start,$length);
    }
    // a function to return the pipArray without the corresponding values
    public function with_out_ids($values,$start = -1,$length = 0){
        if(!is_array($values))
            return $this->with_ids([$values]);
        $rests = self::with_id_conds($values,"AND");
        return $this->_gets_($rests["values"],[],$rests["conds"],"",$start,$length,"!=");
    }
    private static function with_id_conds($values,$_CONDS = "OR"){
        $Conds = [];
        $newValues = [];
        for($ii=0;$ii<sizeof($values);$ii++){
          if(is_numeric($values[$ii]))
              array_push($newValues,$values[$ii]);
          
        }
        for($ii=0;$ii<sizeof($newValues);$ii++)
            array_push($Conds,$_CONDS);
        return [
            "values"=>$newValues,
            "conds"=>$Conds
        ];
    }
    // a function that will fecth data with comparison operator of >
    public function _gets($fields=[],$values=[],$conds=[],$order="",$start=-1,$lenght=0){
        return $this->_gets_($fields,$values,$conds,$order,$start,$lenght,">");
    }
    // a public function to fetch all datas with comparison operator of <
    public function gets_($fields=[],$values=[],$conds=[],$order="",$start=-1,$lenght=0){
        return $this->_gets_($fields,$values,$conds,$order,$start,$lenght,"<");
    }
    // a public function to fetch all data with a choseen operator between =, >=, <= , <, > or !=
    public function get_s($operator,$fields=[],$values=[],$conds=[],$order="",$start=-1,$lenght=0){
        return $this->_gets_($fields,$values,$conds,$order,$start,$lenght,$operator);
    }
    // a function to check if the given operator is valid
    public static function is_operator($ops){
        if(is_string($ops)){
            if(strlen($ops)){
                if(($ops=="=")||($ops=="<")||($ops==">")||($ops==">=")||($ops=="<=")||($ops=="!="))
                    return true;
                else  return false;
            } else return false; 
        } else return false;
    }
    // a function that will fetch datas with the bracket datas input
    
    public function GETS($brackets,$order="",$start=-1,$lenght=0){
        $this_ids = bracketDatas(delBrackets($brackets,0));
        
        // adding a random value if delBrackets() returns an empty array, to prevent form retrieving all records from database 
        if(sizeof($this_ids)==0){
            return pipArr([]);
        }
        return $this->_gets_($this_ids,[],[],$order="",$start = -1,$lenght = 0);
    }
    // function that will fetch all datas from the table with specified limit where $start is the starting point and $length is the length of the the results
    public function _gets_LIMIT($start,$lenght){
	 	$this->sql = "SELECT * FROM `$this->table_name` LIMIT $start,$lenght";
	 	return new PIP_Array($this->SELECT_DATAS());
 	}
    // function that will fetch all datas from the table with specified limit and $order is the field to order from
    public function _gets_ORDER_LIMIT($order,$start,$lenght){
        if($order=='RAND'){
            $order = 'RAND()';
        } if(is_numeric($order)){
            $order = "`$this->table_name`.`$this->primary_key`";
        } else { $order = "`$order`"; } 
	 	$this->sql = "SELECT * FROM `$this->table_name` ORDER BY $order DESC LIMIT $start,$lenght";
	 	return new PIP_Array($this->SELECT_DATAS());
 	}
    // function that will fetch all datas from the table where $order is the field to order from
    public function _gets_ORDER($order){
        if($order=='RAND'){
            $order = 'RAND()';
        } else { $order = "`$order`"; } 
	 	$this->sql = "SELECT * FROM `$this->table_name` ORDER BY $order DESC";
	 	return new PIP_Array($this->SELECT_DATAS());
 	}
    // function that will fetch all datas from the table where $fields and $values are lists of field and coresponding values respectively and $conds is the array of logic operators "OR" and "AND" 
    public function _gets__($fields,$values,$conds, $ops = "="){
       $this->sql = "SELECT * FROM `$this->table_name`"; 
       $opsx = "=";
        if(!is_array($ops)){
            if(self::is_operator($ops))
               $opsx = $ops;
        }
        for($ii=0;$ii<sizeof($fields);$ii++){
          if(is_array($ops))
              if(isset($ops[$ii]))
                 if(self::is_operator($ops[$ii]))
                    $opsx = $ops[$ii];
          
          if($ii==0){
            $this->sql.= " WHERE `$fields[$ii]` $opsx '$values[$ii]'";  
          } else {
            if(isset($conds[$ii-1]))
                $condsxx = $conds[$ii-1];
            else $condsxx = "AND";
            $this->sql.= " ".$condsxx." `$fields[$ii]` $opsx '$values[$ii]'"; 
          }  
        }
       return new PIP_Array($this->SELECT_DATAS());
       
    }
    // function with the same features as _gets__() but with limited results as _gets_LIMIT
    public function _gets__LIMIT($fields,$values,$conds,$start,$lenght, $ops = "="){
       $this->sql = "SELECT * FROM `$this->table_name`"; 
       $opsx = "=";
       if(!is_array($ops)){
            if(self::is_operator($ops))
               $opsx = $ops;
        }
        for($ii=0;$ii<sizeof($fields);$ii++){
            if(is_array($ops))
              if(isset($ops[$ii]))
                 if(self::is_operator($ops[$ii]))
                    $opsx = $ops[$ii];
          if($ii==0){
            $this->sql.= " WHERE `$fields[$ii]` $opsx '$values[$ii]'";  
          } else {
            $this->sql.= " ".$conds[$ii-1]."`$fields[$ii]` $ops '$values[$ii]'"; 
          }  
        }
       return new PIP_Array($this->SELECT_DATAS());
       
    }
    // function with the same features as _gets__() but with limited results as _gets_LIMIT and ORDER CONDITION as _gets_ORDER_LIMIT() 
    public function _gets__ORDER_LIMIT($fields,$values,$conds,$order,$start,$lenght, $ops = "="){
       if($order=='RAND'){
            $order = 'RAND()';
        } else { $order = "`$order`"; }
       $this->sql = "SELECT * FROM `$this->table_name`"; 
       $opsx = "=";
       if(!is_array($ops)){
            if(self::is_operator($ops))
               $opsx = $ops;
        }
        for($ii=0;$ii<sizeof($fields);$ii++){
          if(is_array($ops))
              if(isset($ops[$ii]))
                 if(self::is_operator($ops[$ii]))
                    $opsx = $ops[$ii];
          if($ii==0){
            $this->sql.= " WHERE `$fields[$ii]` $opsx '$values[$ii]'";  
          } else {
            $this->sql.= " ".$conds[$ii-1]."`$fields[$ii]` $opsx '$values[$ii]'"; 
          }  
        }
       $this->sql = $this->sql." ORDER BY $order DESC $start,$lenght";
       return new PIP_Array($this->SELECT_DATAS());
       
    }
    // function with the same features as _gets__() but with ORDER CONDITION as _gets_ORDER() 
    public function _gets__ORDER($fields,$values,$conds,$order, $ops = "="){
       if($order=='RAND'){
            $order = 'RAND()';
        } else { $order = "`$order`"; }
       $this->sql = "SELECT * FROM `$this->table_name`"; 
       $opsx = "=";
       if(!is_array($ops)){
            if(self::is_operator($ops))
               $opsx = $ops;
       }
       if(is_array($ops))
              if(isset($ops[$ii]))
                 if(self::is_operator($ops[$ii]))
                    $opsx = $ops[$ii];
        for($ii=0;$ii<sizeof($fields);$ii++){
          if($ii==0){
            $this->sql.= " WHERE `$fields[$ii]` $opsx '$values[$ii]'";  
          } else {
            $this->sql.= " ".$conds[$ii-1]."`$fields[$ii]` $opsx '$values[$ii]'"; 
          }  
        }
       $this->sql = $this->sql." ORDER BY $order DESC";
       return new PIP_Array($this->SELECT_DATAS());
       
    }
    // function that will fetch datas from the table joined with all parents tables but with Specified parameter for join option like INNER, OUTER, LEFT, AND RIGHT. and $GRAND_PARENT_JOINS is a 2d array specify the all grandparent JOINS
    // and $FIELDS is the filds to check condition for $VALUE defined and $table is the name of the table the field is from
    public function __gets_($JOIN,$GRAND_PARENT_JOINS = [], $FIELDS = [], $TABS = [],$VALUE = [], $COND = [], $ops = "="){
        $this->sql = "SELECT * FROM `$this->table_name`";
        $opsx = "=";
        if(sizeof($JOIN)<=sizeof($this->parents)){
            for($ii=0;$ii<sizeof($JOIN);$ii++){
                $this->sql = $this->sql." "
                    .$JOIN[$ii]." JOIN `"
                    .$this->parents[$ii]->GET_TAB()->table_name
                    ."` ON `$this->table_name`.`"
                    .$this->parents[$ii]->GET_FORE()."` = `"
                    .$this->parents[$ii]->GET_TAB()->table_name
                    ."`.`".$this->parents[$ii]->GET_TAB()->primary_key
                    ."`";
            }
            if(sizeof($GRAND_PARENT_JOINS)<=sizeof($this->parents)){
                for($ii=0;$ii<sizeof($GRAND_PARENT_JOINS);$ii++){
                    if(sizeof($GRAND_PARENT_JOINS[$ii])<=sizeof($this->parents[$ii]->GET_TAB()->parents)){
                        for($iii=0;$iii<sizeof($GRAND_PARENT_JOINS[$ii]);$iii++){
                            $this->sql = $this->sql." "
                                 .$GRAND_PARENT_JOINS[$ii][$iii]." JOIN `"
                                 .$this->parents[$ii]->GET_TAB()->parents[$iii]->GET_TAB()->table_name
                                 ."` ON `".$this->parents[$ii]->GET_TAB()->table_name."`.`"
                                 .$this->parents[$ii]->GET_TAB()->parents[$iii]->GET_FORE()."` = `"
                                 .$this->parents[$ii]->GET_TAB()->parents[$iii]->GET_TAB()->table_name
                                 ."`.`".$this->parents[$ii]->GET_TAB()->parents[$iii]->GET_TAB()->primary_key
                                 ."`";
                        }
                    }
                }
            }
            
            if(sizeof($FIELDS)){
                if(sizeof($FIELDS)==sizeof($TABS)){
                    if(sizeof($FIELDS)==sizeof($VALUE)){
                        for($ii=0;$ii<sizeof($FIELDS);$ii++){
                            if(is_array($ops))
                              if(isset($ops[$ii]))
                                 if(self::is_operator($ops[$ii]))
                                    $opsx = $ops[$ii];
                            if(!sizeof($COND)){
                                $this->sql = $this->sql." WHERE `".$TABS[$ii]."`.`".$FIELDS[$ii]."` $opsx "."'".$VALUE[$ii]."' "; 
                            } else {
                                if($ii){
                                  $this->sql = $this->sql."".$COND[$ii-1]." `".$TABS[$ii]."`.`".$FIELDS[$ii]."` = "."'".$VALUE[$ii]."' "; 
                                } else {
                                  $this->sql = $this->sql." WHERE `".$TABS[$ii]."`.`".$FIELDS[$ii]."` $opsx "."'".$VALUE[$ii]."' ";  
                                }
                            }
                        }
                    }
                }
            }
            
            
        }
        return new PIP_Array($this->SELECT_DATAS());
    }
    // function to count number of records of this table
    public function counts(){
        $this->sql = "SELECT COUNT($this->primary_key) AS Numberof FROM $this->table_name";
        $this->conn = $this->_connection_();
        if($result = mysqli_query($this->conn,$this->sql)){
            return mysqli_fetch_array($result)[0];
        } else {
            $this->conn->close();
            $this->conn = 0;
            return -5;
        }
    }
    //a function to check if the given id exist in the database
    public function exist($ids){
        return $this-> counts_([$this->id()],[$ids]);
    }
    //function with the same features as counts() but with fields list matching values in $FIELD array and $VALUE array respectively also with list of logic operators
    public function counts_($FIELD,$VALUE,$conds = [], $ops = "="){
        $this->sql = "SELECT COUNT($this->primary_key) AS totalnumber FROM `$this->table_name`";
        $opsx = "=";
        for($ii=0;$ii<sizeof($FIELD);$ii++){
            if(is_array($ops))
              if(isset($ops[$ii]))
                 if(self::is_operator($ops[$ii]))
                    $opsx = $ops[$ii];
            if($ii<1)
                $this->sql .= " WHERE `$FIELD[$ii]` $opsx '$VALUE[$ii]' ";
            else $this->sql .= $conds[$ii-1]." `$FIELD[$ii]` $opsx '$VALUE[$ii]'";
        }
        $this->conn = $this->_connection_();
        $result = mysqli_query($this->conn,$this->sql);
        $this->conn->close();
        $this->conn = 0;
        return mysqli_fetch_array($result)[0];
    }
    public function register($existing,$testing,$fields,$values){
 		$POST_VALUES = array();
        $this->conn = $this->_connection_();
        $status = false;
        if(!(sizeof($existing)==sizeof($testing))){
            $this->message = "Testing and Existing fields must have the same size";
            return $status;
        } 
        else {
            for ($i=0; $i < sizeof($existing); $i++) { 
                $var = $testing[$i];
                if(isset($_POST[$var])){
                    array_push($POST_VALUES,
                               mysqli_real_escape_string($this->conn,
                                                         stripslashes($_POST[$var])));
                }
                    
                else if(isset($_GET[$var])){
                    array_push($POST_VALUES,
                               mysqli_real_escape_string($this->conn,
                                                         stripslashes($_GET[$var])));
                } else {
                    array_push($POST_VALUES,
                               mysqli_real_escape_string($this->conn,
                                                         stripslashes($var)));
                }
                    
            }
            $conds = [];
            for($ii=0;$ii<sizeof($existing);$ii++){
                array_push($conds,"OR");
            }
            
            $this_admin = $this->_gets_($existing,$POST_VALUES,$conds);
            $existing_fields = "";
            $found = false;

            for($ii=0;$ii<sizeof($existing);$ii++){
               if($this_admin->filterthis($existing[$ii],
                                          $POST_VALUES[$ii],"O")->size>0){
                   $existing_fields = $existing_fields."$existing[$ii] ,";
                   $found = true;
               } 
            }
            

            if($found){
                $this->message = "$existing_fields you entered is allready exist and must be unique in the system";
                return 0;
            } 
            else {
                $status = 1;
                $this->message = "all information you entered are unique in the system";
                return $this->_add_($fields,$values);
            }
        }
 	}
 	public function register_with_current_date($existing,$testing,$fields,$values,$date_field){
        
 		$POST_VALUES = [];
        $this->conn = $this->_connection_();
        if(!(sizeof($existing)==sizeof($testing))){
            $this->message = "Testing and Existing fields must have the same size";
            return 0;
        } else {
            for ($i=0; $i < sizeof($existing); $i++) { 
                $var = $testing[$i];
                if(isset($_POST[$var])){
                    array_push($POST_VALUES,
                                   mysqli_real_escape_string($this->conn,
                                                             stripslashes($_POST[$var])));
                } else if(isset($_GET[$var])){
                    array_push($POST_VALUES,
                                   mysqli_real_escape_string($this->conn,
                                                             stripslashes($_GET[$var])));
                } else {
                    array_push($POST_VALUES,
                                   mysqli_real_escape_string($this->conn,
                                                             stripslashes($var)));
                }	
            }
        
            $conds = [];
            for($ii=0;$ii<sizeof($existing);$ii++){
                array_push($conds,"OR");
            }
            
            $this_admin = $this->_gets_($existing,$POST_VALUES,$conds);
            $existing_fields = "";
            $found = false;

            for($ii=0;$ii<sizeof($existing);$ii++){
               if($this_admin->filterthis($existing[$ii],$POST_VALUES[$ii],"O")->size>1){
                   $existing_fields = $existing_fields."$existing[$ii] ,";
                   $found = true;
               } 
            }

            if($found){
                $this->message = "$existing_fields you entered is allready exist and must be unique in the system";
                return 0;
            } 
            else {
                $status = 1;
                $this->message = "all information you entered are unique in the system";
                return $this->_add_($fields,$values,$date_field);
            } 
        }
 		
 	}
    public function register_($existing,$testing,$fields,$values,$date_field=""){
        if($date_field=="") {
            return $this->register($existing,$testing,$fields,$values);
        } 
        else {
            return $this->register_with_current_date($existing,$testing,$fields,$values,$date_field);
        }
    } 
    // a function to check if the child element exist 
    public function isChild($table_name){
        $exist = false;
        if(isset($this->child[$table_name])){
            return true;
        } else {
            for($ii=0;$ii<sizeof($this->child);$ii++){
                if(isset($this->child[$ii]))
                    if($this->child[$ii]->GET_TAB()->table_name==$table_name){
                        $exist = true;
                        break;
                    }
                        
            }
        }
        
        return $exist;
    }
    // a private function to check if parent element exist
    public function isParent($table_name){
        $exist = false;
        if(isset($this->parents[$table_name])){
            return true;
        } else {
            for($ii=0;$ii<sizeof($this->parents);$ii++){
                if(isset($this->parents[$ii]))
                    if($this->parents[$ii]->GET_TAB()->table_name==$table_name){
                        $exist = true;
                        break;
                    }
            }
        }
        return $exist;
    }
    /* 
      #####################################################################
      
         following functions are for joining table from the same database and server where child() is for joing a table as a child of this object and parent_() is to add another table as a parent of the object and finaly parent__() is the same as the parent_() but will be used if the primary key are the same as the foreign one 
    
    */
    // functions that will add a table to join with the foreign key equal to the primary key of this one
    // where $TABLES is the name of the table and $FOREIGNS are the foreign keys of those tables
    // and $PRIMARY are primary keys of those tables 
    public function child($TABLES,$PRIMARY,$FOREIGNS){
        if(sizeof($TABLES)==sizeof($FOREIGNS)){
            for($ii=0;$ii<sizeof($TABLES);$ii++){
                array_push($this->child,
                           new tab_foreign(new admin($TABLES[$ii],$PRIMARY[$ii],$this->conn),$FOREIGNS[$ii]));
                
                $this->child[$TABLES[$ii]] = new tab_foreign(new admin($TABLES[$ii],$PRIMARY[$ii],$this->conn),$FOREIGNS[$ii]);
            }
            return $this;
        } else return $this;
    }
    // a public function to similar to the above but when forein and primary are similar
    public function child_($TABLES,$PRIMARY){
        if(sizeof($TABLES)==sizeof($PRIMARY)){
            $FOREIGNS = array();
            for($i=0;$i<sizeof($TABLES);$i++){
              $FOREIGNS[$i] = $this->primary_key;  
            }
            return $this->child($TABLES,$PRIMARY,$FOREIGNS);
        } else return $this;
        
    }
    // a public function to add a child table with full table and and foreign key only
    public function _child_(&$TABLES,$PRIMARY = ""){
            if(is_array($PRIMARY)&&is_array($TABLES)){
               if(sizeof($TABLES)==sizeof($PRIMARY)){
                for($i=0;$i<sizeof($TABLES);$i++){
                    if($PRIMARY[$i]==""){
                        if(!$this->isChild($TABLES[$i]->table_name)){
                            array_push($this->child,
                                       new tab_foreign($TABLES[$i],$this->primary_key));
                            $this->child[$TABLES[$i]->table_name] = new tab_foreign($TABLES[$i],$this->primary_key);
                        } 
                           
                        if(!$TABLES[$ii]->isParent($this->table_name)){
                            array_push($TABLES[$i]->parents,
                                       new tab_foreign($this,$this->primary_key));
                            $TABLES[$i]->parents[$this->table_name] = new tab_foreign($this,$this->primary_key);
                        }
                        
                        
                    } else {
                        if(!$this->isChild($TABLES[$i]->table_name)){
                            array_push($this->child,
                                       new tab_foreign($TABLES[$i],$PRIMARY[$i]));
                            $this->child[$TABLES[$i]->table_name] = new tab_foreign($TABLES[$i],$PRIMARY[$i]); 
                        }
                        
                        if(!$TABLES[$ii]->isParent($this->table_name)){
                            array_push($TABLES[$i]->parents,
                                       new tab_foreign($this,$PRIMARY[$i]));
                            $TABLES[$i]->parents[$this->table_name] = new tab_foreign($this,$PRIMARY[$i]);
                        }
                    }
                }
              }
            } else if(($PRIMARY=="")&&(!is_array($TABLES))){
                if(!$this->isChild($TABLES->table_name)){
                    array_push($this->child, new tab_foreign($TABLES,$this->primary_key));
                    $this->child[$TABLES->table_name] = new tab_foreign($TABLES,$this->primary_key);
                }
                  
                
                if(!$TABLES->isParent($this->table_name)){
                    array_push($TABLES->parents, new tab_foreign($this,$this->primary_key));
                    $TABLES->parents[$this->table_name] = new tab_foreign($this,$this->primary_key);   
                }
                
                
            } else if(!is_array($TABLES)){
                if(!$this->isChild($TABLES->table_name)){
                    array_push($this->child, new tab_foreign($TABLES,$PRIMARY));
                    $this->child[$TABLES->table_name] = new tab_foreign($TABLES,$PRIMARY);
                }
                
                if(!$TABLES->isParent($this->table_name)){
                    array_push($TABLES->parents, new tab_foreign($this,$PRIMARY));
                    $TABLES->parents[$this->table_name] = new tab_foreign($this,$PRIMARY);   
                }  
            }
       return $this;
    }
    // functions that will add a table to join with the primary key equal to the foreign key of this one
    // where $TABLES is the name of the table and $FOREIGNS are the foreign keys of those tables
    // and $PRIMARY are primary keys of those tables
    public function parent__($TABLES,$PRIMARY,$FOREIGNS){
        if(sizeof($TABLES)==sizeof($FOREIGNS)){
            for($ii=0;$ii<sizeof($TABLES);$ii++){
                $tab = new admin($TABLES[$ii],$PRIMARY[$ii],$this->conn);
                $childs = new tab_foreign($tab,$FOREIGNS[$ii]);
                //print_r($childs);
                array_push($this->parents,$childs);
            }
           return $this;
        } else return $this;
    }
    // the same as the table above but if the foreign key is similar to the primary key of the parent table
    public function parent_($TABLES,$PRIMARY){
        if(sizeof($TABLES)==sizeof($PRIMARY)){
            for($ii=0;$ii<sizeof($TABLES);$ii++){
                $tab = new admin($TABLES[$ii],$PRIMARY[$ii],$this->conn);
                $childs = new tab_foreign($tab,$PRIMARY[$ii]);
                array_push($this->parents,$childs);
            }
           return $this;
        } else return $this;
    }
    // a public function to add a parent table with the entire one.
    public function _parent_(&$TABLES,$PRIMARY = ""){
        if(is_array($PRIMARY)&&is_array($TABLES)){
           if(sizeof($TABLES)==sizeof($PRIMARY)){
            for($i=0;$i<sizeof($TABLES);$i++){
                if($PRIMARY[$i]==""){
                    if(!$this->isParent($TABLES[$i]->table_name)){
                        array_push($this->parents, 
                                   new tab_foreign($TABLES[$i],$TABLES[$i]->primary_key));
                        $this->parents[$TABLES[$ii]->table_name] = 
                            new tab_foreign($TABLES[$i],$TABLES[$i]->primary_key);
                    }

                    if(!$TABLES[$i]->isChild($this->table_name)){
                        array_push($TABLES[$i]->child, 
                                   new tab_foreign($this,$TABLES->primary_key));
                        $TABLES[$i]->child[$this->table_name] = 
                            new tab_foreign($this,$TABLES->primary_key);
                    }


                } else {
                    if(!$this->isParent($TABLES[$i]->table_name)){
                       array_push($this->parents, new tab_foreign($TABLES[$i],$PRIMARY[$i]));
                       $this->parents[$TABLES[$i]->table_name] = 
                           new tab_foreign($TABLES[$i],$PRIMARY[$i]);
                    }

                    if(!$TABLES[$i]->isChild($this->table_name)){
                        array_push($TABLES[$i]->child,
                                   new tab_foreign($this,$PRIMARY[$i]));
                        $TABLES[$i]->child[$this->table_name] = 
                            new tab_foreign($this,$PRIMARY[$i]);

                    }
                }
             }
           }
        } 
        else if(($PRIMARY=="")&&(!is_array($TABLES))){
                if(!$this->isParent($TABLES->table_name)){
                    array_push($this->parents, 
                               new tab_foreign($TABLES,$TABLES->primary_key));
                    $this->parents[$TABLES->table_name] = 
                        new tab_foreign($TABLES,$TABLES->primary_key);
                }

                if(!$TABLES->isChild($this->table_name)){
                    array_push($TABLES->child, 
                               new tab_foreign($this,$TABLES->primary_key));
                    $TABLES->child[$this->table_name] = 
                        new tab_foreign($this,$TABLES->primary_key);
                }
       } 
        else if(!is_array($TABLES)){
            if(!$this->isParent($TABLES->table_name)){
               array_push($this->parents, new tab_foreign($TABLES,$PRIMARY));
               $this->parents[$TABLES->table_name] = 
                   new tab_foreign($TABLES,$PRIMARY);
            }
            if(!$TABLES->isChild($this->table_name)){
                array_push($TABLES->child,
                           new tab_foreign($this,$PRIMARY));
                $TABLES->child[$this->table_name] = 
                    new tab_foreign($this,$PRIMARY);
            }
       }
        return $this;
    }
    // a public function to join tables with others but by chaining
    public function JnC($childs, $join = "INNER"){
        $joinp = new JOINING($this);
        if(!contains_arr(admin::JOINS,$join)) $join = "INNER";
        return $joinp->JOINC($childs,$join)->tabs;
    }
    // a public function the same as above but with initial child or parent Query
    public function Jnn($childs,$child_parent,$join = "INNER"){
        $joinp = new JOINING($this);
        if(!contains_arr(admin::JOINS,$join)) $join = "INNER";
        return $joinp->JOINPP($childs,$child_parent,$join)->tabs;
    }
    public function JnP($parent, $join = "INNER"){
        $joinp = new JOINING($this);
        if(!contains_arr(admin::JOINS,$join)) $join = "INNER";
        return $joinp->JOINP($parent,$join)->tabs;
    }
    public function where($val,$fields = "",$tab = "",$bool = "", $sign = "="){
        $joinp = new JOINING($this);
        return $joinp->Cond($val,$fields,$tab,$bool,$sign)->tabs;
    }
    // a function to get data from joined tables
    public function get($fields="",$table="",$AZZ = ""){
        $Comp_fields = "";
        if(substr($this->sql,0,6)=="SELECT"){
            if(is_array($fields)&&is_array($table)){
                for($ii=0;$ii<sizeof($fields);$ii++){
                    if(isset($table[$ii])){
                        $table_n = $table[$ii];
                        if($table[$ii] instanceof admin){
                            $table_n =  $table[$ii]->table_name;
                        } else if($table[$ii]==""){
                            $table_n = $this->table_name;
                        }
                        $AZZx = "";
                        if(is_array($AZZ)){
                            if(isset($AZZ[$ii])){
                                $AZZx = " AS ".$AZZ[$ii];
                                if($AZZ[$ii]==""){
                                   $AZZx = ""; 
                                }
                            } else {
                                $AZZx = "";
                            }
                        } else $AZZx = "";
                        $Comp_fields = $Comp_fields."`$table_n`.`".$fields[$ii]."` $AZZx ";
                    } else {
                        $AZZx = "";
                        if(is_array($AZZ)){
                            if(isset($AZZ[$ii])){
                                $AZZx = " AS ".$AZZ[$ii];
                                if($AZZ[$ii]==""){
                                   $AZZx = ""; 
                                }
                            } else {
                                $AZZx = "";
                            }
                        } else $AZZx = "";
                        
                        $Comp_fields = $Comp_fields."`".$this->table_name."`.`".$fields[$ii]."` $AZZx ";
                    }
                    if($ii<(sizeof($fields)-1)){
                        $Comp_fields .= ",";
                    }
                }
            }
            else if(is_array($fields)){
               $new_table = [];
               for($ii=0;$ii<sizeof($fields);$ii++){
                   array_push($new_table,$table);
               }
               return $this->get($fields,$new_table,$AZZ);
            }
            else if(($fields=="")&&($table=="")){
                return new  PIP_Array($this->SELECT_DATAS());
            } else if(!($fields=="")){
                if($table instanceof admin){
                   $table =  $table->table_name; 
                } else {
                   $table = $this->table_name;
                }
                if(is_array($AZZ)){
                    if(isset($AZZ[0])){
                        $AZZ = " AS ".$AZZ[0];
                        if($AZZ[0]=="")
                            $AZZ = "";
                    } else {
                        $AZZ = "";
                    }
                } else if($AZZ=="") {
                    $AZZ = "";
                }
                else $AZZ = " AS ".$AZZ;
                $Comp_fields = "`$table`.`$fields` $AZZ";
            }
            $this->sql = str_replace('*',$Comp_fields,$this->sql);
            return new PIP_Array($this->SELECT_DATAS());
        } else {
            $admin[0][0] = -1;
            $admin[0]["message_returneds_failed_pip_array"] 
                = "the query given is not for selection check the last ";
            $admin[0]["size_returned_failed_pip_array"] = "0 records found";
            return new PIP_Array($admin);
        } 
    }
    // a private function to add a range and limit to the composed SQL of the JOINED TABLES
    private function range($INPUT,&$joinp){
        if(isset($INPUT["RANGE"])){
            $start = -5;
            
            if(isset($INPUT["RANGE"]["FROM"])){
                if(is_numeric($INPUT["RANGE"]["FROM"])){
                    $start = intval($INPUT["RANGE"]["FROM"]);
                }
            }
            $end = -5;
            
            if(isset($INPUT["RANGE"]["LENGTH"])){
                if(is_numeric($INPUT["RANGE"]["LENGTH"]))
                    $end = intval($INPUT["RANGE"]["LENGTH"]);
            }
            
            $tabs = $this->table_name;
            $field = $this->primary_key;
            if(isset($INPUT["RANGE"]["TABS"])){
                $tabs = $INPUT["RANGE"]["TABS"];
            }
            if(isset($INPUT["RANGE"]["FIELD"])){
                $field = $INPUT["RANGE"]["FIELD"];
            }
            
            $order = 0;
            if(isset($INPUT["RANGE"]["ORDER"]))
                $order = $INPUT["RANGE"]["ORDER"];
           $joinp->range($tabs,$field,$order,$start,$end); 
        }
    }
    // a private function to find an appropriate logic operation for multiple field and multiple values
    private function logic($INPUT,$index){
        $logic = "OR";
        if(isset($INPUT["CONDS"])){
            if(is_array($INPUT["CONDS"]))
                if(isset($INPUT["CONDS"][$index]))
                    if($INPUT["CONDS"][$index]=="AND")
                      $logic = "AND";  
        }
        return $logic;
    }
    // a private function to find an appropriate operator with valid input for the fetch function
    private function op($INPUT,$index = -10){
        $ops = "=";
        if(isset($INPUT["OP"])){
            if($index>=0){
                if(is_array($INPUT["OP"])){
                    if(isset($INPUT["OP"][$index])){
                        if(admin::is_operator($INPUT["OP"][$index]))
                           $ops =  $INPUT["OP"][$index];
                    }
                }
            } 
            else {
                if(is_array($INPUT["OP"]))
                    return $this->op($INPUT["OP"],0);
                else if(admin::is_operator($INPUT["OP"])){
                    $ops = $INPUT["OP"];
                }
            }
        }
        //echo $index."<br>";
        return $ops;
    }
    
    // a new function to fetch data from the table with specified input conditions where it can be an array, a PIP_Array an index or null 
    // on the array side indexes must be follow: RANGE,TABS,FIELDS,VALUES,CONDS,SIGNS,ORDERS,TAB_ORDERS
    public function fetch($fields="",$table="",$AZZ = "",$INPUT = NULL){
        switch($INPUT){
            case is_array($INPUT):{
                if(isset($INPUT["TABS"])){
                    if(is_array($INPUT["TABS"])){
                        $joinp = new JOINING($this);
                        for($ii=0;$ii<sizeof($INPUT["TABS"]);$ii++){
                            if($INPUT["TABS"][$ii] instanceof admin){
                                if(contains_arr($this->joined_child,$INPUT["TABS"][$ii]->table_name)||
                                   contains_arr($this->joined_parent,$INPUT["TABS"][$ii]->table_name)||
                                   ($INPUT["TABS"][$ii]->table_name==$this->table_name)){
                                    if(isset($INPUT["FIELDS"])&&isset($INPUT["VALUES"])){
                                        if(is_array($INPUT["FIELDS"])&&is_array($INPUT["VALUES"])){
                                            if(isset($INPUT["VALUES"][$ii])){
                                                $fieldsx = $INPUT["TABS"][$ii]->primary_key;
                                                if(isset($INPUT["FIELDS"][$ii])){
                                                    if($INPUT["TABS"][$ii]->isField($INPUT["FIELDS"][$ii])){
                                                        $fieldsx = $INPUT["FIELDS"][$ii];
                                                    }
                                                }
                                                $joinp->Cond($INPUT["VALUES"][$ii],
                                                             $INPUT["TABS"][$ii],
                                                             $fieldsx,
                                                             $this->logic($INPUT,$ii),
                                                             $this->op($INPUT,$ii));
                                            }
                                            else {
                                                continue;
                                            }
                                        }
                                        else if(is_array($INPUT["VALUES"])){
                                            if(isset($INPUT["VALUES"][$ii])){
                                                $fieldsx = $INPUT["TABS"][$ii]->primary_key;
                                                if($INPUT["TABS"][$ii]->isField($INPUT["FIELDS"])){
                                                        $fieldsx = $INPUT["FIELDS"];
                                                }
                                                $joinp->Cond($INPUT["VALUES"][$ii],
                                                             $INPUT["TABS"][$ii],
                                                             $fieldsx,
                                                             $this->logic($INPUT,$ii),
                                                             $this->op($INPUT,$ii));
                                            }
                                            else {
                                                continue;
                                            }
                                        } 
                                        else {
                                            $fieldsx = $INPUT["TABS"][$ii]->primary_key;
                                            if($INPUT["TABS"][$ii]->isField($INPUT["FIELDS"])){
                                                $fieldsx = $INPUT["FIELDS"];
                                            }
                                            $joinp->Cond($INPUT["VALUES"],
                                                         $INPUT["TABS"][$ii],
                                                         $fieldsx,
                                                         $this->logic($INPUT,$ii),
                                                         $this->op($INPUT,$ii));
                                        }
                                    } 
                                    else if(isset($INPUT["VALUES"])){
                                        if(is_array($INPUT["VALUES"])){
                                            for($i=0;$i<sizeof($INPUT["VALUES"]);$i++){
                                                $joinp->Cond($INPUT["VALUES"][$i],$INPUT["TABS"][$ii],"","OR");
                                            }
                                        } else {
                                            if(($INPUT["TABS"][$ii]==$this->table_name)&&is_numeric($INPUT["VALUES"]))
                                                $joinp->Cond($INPUT["VALUES"],
                                                             "",
                                                             "",
                                                             $this->logic($INPUT,$ii),
                                                             $this->op($INPUT,$ii));
                                            else {
                                                $joinp->Cond($INPUT["VALUES"],
                                                             $INPUT["TABS"][$ii],
                                                             "",
                                                             $this->logic($INPUT,$ii),
                                                             $this->op($INPUT,$ii));
                                            }
                                        }
                                    } 
                                    else {
                                        break;
                                    }
                                } else {
                                    continue;
                                }
                            } 
                            else {
                                if(contains_arr($this->joined_child,$INPUT["TABS"][$ii])||
                                   contains_arr($this->joined_parent,$INPUT["TABS"][$ii])||
                                   ($INPUT["TABS"]==$this->table_name)){
                                    if(isset($INPUT["FIELDS"])&&isset($INPUT["VALUES"])){
                                        if(is_array($INPUT["FIELDS"])&&is_array($INPUT["VALUES"])){
                                            if(isset($INPUT["VALUES"][$ii])){
                                                $fieldsx = $this->primary_key;
                                                if(isset($INPUT["FIELDS"][$ii])){
                                                    if($this->isChild($INPUT["TABS"][$ii])){
                                                        if($this->child[$INPUT["TABS"][$ii]]->GET_TAB()->isField($INPUT["FIELDS"][$ii]))
                                                            $fieldsx = $INPUT["FIELDS"][$ii];
                                                        else $fieldsx = $this->child[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                                    }
                                                    else if($this->isParent($INPUT["TABS"][$ii])){
                                                        if($this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->isField($INPUT["FIELDS"][$ii]))
                                                            $fieldsx = $INPUT["FIELDS"][$ii];
                                                        else $fieldsx = $this->child[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                                    }
                                                    $joinp->Cond($INPUT["VALUES"][$ii],
                                                                 $INPUT["TABS"][$ii],
                                                                 $fieldsx,
                                                                 $this->logic($INPUT,$ii),
                                                                 $this->op($INPUT,$ii));
                                                }
                                            } else {
                                                continue;
                                            }
                                        } else if(is_array($INPUT["VALUES"])){
                                            if(isset($INPUT["VALUES"][$ii])){
                                                $fieldsx = $this->primary_key;
                                                if($this->isChild($INPUT["TABS"][$ii])){
                                                    if($this->child[$INPUT["TABS"][$ii]]->GET_TAB()->isField($INPUT["FIELDS"]))
                                                        $fieldsx = $INPUT["FIELDS"];
                                                    else $fieldsx = $this->child[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                                }
                                                else if($this->isParent($INPUT["TABS"][$ii])){
                                                    if($this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->isField($INPUT["FIELDS"]))
                                                        $fieldsx = $INPUT["FIELDS"];
                                                    else $fieldsx = $this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                                }
                                                $joinp->Cond($INPUT["VALUES"][$ii],
                                                             $INPUT["TABS"][$ii],
                                                             $fieldsx,
                                                             $this->logic($INPUT,$ii),
                                                             $this->op($INPUT,$ii));
                                            } else {
                                                continue;
                                            }
                                        } else {
                                            $fieldsx = $this->primary_key;
                                            if($this->isChild($INPUT["TABS"][$ii])){
                                                if($this->child[$INPUT["TABS"][$ii]]->GET_TAB()->isField($INPUT["FIELDS"]))
                                                    $fieldsx = $INPUT["FIELDS"];
                                                else $fieldsx = $this->child[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                            }
                                            else if($this->isParent($INPUT["TABS"][$ii])){
                                                if($this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->isField($INPUT["FIELDS"]))
                                                    $fieldsx = $INPUT["FIELDS"];
                                                else $fieldsx = $this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                            }
                                            $joinp->Cond($INPUT["VALUES"],
                                                         $INPUT["TABS"][$ii],
                                                         $fieldsx,
                                                         $this->logic($INPUT,$ii),
                                                         $this->op($INPUT,$ii));
                                        }
                                    } else if(isset($INPUT["VALUES"])){
                                        if(is_array($INPUT["VALUES"])){
                                            if(isset($INPUT["VALUES"][$ii])){
                                                $fieldsx = $this->primary_key;
                                                if($this->isChild($INPUT["TABS"][$ii])){
                                                    $fieldsx = $this->child[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                                } else if($this->isParent($INPUT["TABS"][$ii])){
                                                    $fieldsx = $this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                                }
                                                $joinp->Cond($INPUT["VALUES"][$ii],
                                                             $INPUT["TABS"][$ii],
                                                             $fieldsx,
                                                             $this->logic($INPUT,$ii),
                                                             $this->op($INPUT,$ii));
                                            } else {
                                                continue;
                                            }
                                        } else {
                                            $fieldsx = $this->primary_key;
                                            if($this->isChild($INPUT["TABS"][$ii])){
                                                $fieldsx = $this->child[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                            } else if($this->isParent($INPUT["TABS"][$ii])){
                                                $fieldsx = $this->parents[$INPUT["TABS"][$ii]]->GET_TAB()->primary_key;
                                            }
                                            $joinp->Cond($INPUT["VALUES"],
                                                         $INPUT["TABS"][$ii],
                                                         $fieldsx,
                                                         $this->logic($INPUT,$ii),
                                                         $this->op($INPUT,$ii));
                                        }
                                    } else {
                                        continue;
                                    }
                                } else {
                                    continue;
                                }
                            }
                        }
                        $this->range($INPUT,$joinp);
                        $rets = $joinp->tabs->get($fields,$table,$AZZ);
                        $this->sql = $joinp->tabs->sql;
                        $this->SpreadP();
                        $this->SpreadC();
                        return $rets;
                    } 
                    else {
                        if($INPUT["TABS"] instanceof admin){
                            if(contains_arr($this->joined_child,$INPUT["TABS"]->table_name)||
                               contains_arr($this->joined_parent,$INPUT["TABS"]->table_name)||
                               ($INPUT["TABS"]->table_name==$this->table_name)){
                                $INPUT["TABS"] = $INPUT["TABS"]->table_name;
                                return $this->fetch($fields,$table,$AZZ,$INPUT);
                            } else {
                                return $this->_fetch_();
                            }
                        }
                        else {
                            if(contains_arr($this->joined_child,$INPUT["TABS"])||
                               contains_arr($this->joined_parent,$INPUT["TABS"])||
                               ($INPUT["TABS"]==$this->table_name)){
                                $joinp = new JOINING($this);
                                if(isset($INPUT["FIELDS"])&&isset($INPUT["VALUES"])){
                                    if(is_array($INPUT["FIELDS"])&&is_array($INPUT["VALUES"])){
                                        for($ii=0;$ii<sizeof($INPUT["VALUES"]);$ii++){
                                            $fieldsx = $this->primary_key;
                                            if(isset($INPUT["FIELDS"][$ii])){
                                                if($this->isChild($INPUT["TABS"])){
                                                    if($this->child[$INPUT["TABS"]]->GET_TAB()->isField($INPUT["FIELDS"][$ii]))
                                                        $fieldsx = $INPUT["FIELDS"][$ii];
                                                    else $fieldsx = $this->child[$INPUT["TABS"]]->GET_TAB()->primary_key;
                                                }
                                                else if($this->isParent($INPUT["TABS"])){
                                                    if($this->parents[$INPUT["TABS"]]->GET_TAB()->isField($INPUT["FIELDS"][$ii]))
                                                        $fieldsx = $INPUT["FIELDS"][$ii];
                                                    else $fieldsx = $this->child[$INPUT["TABS"]]->GET_TAB()->primary_key;
                                                }
                                            }  
                                            $joinp->Cond($INPUT["VALUES"][$ii],
                                                         $INPUT["TABS"],
                                                         $fieldsx,
                                                         $this->logic($INPUT,$ii),
                                                         $this->op($INPUT,$ii));
                                        }
                                        $this->range($INPUT,$joinp);
                                        $rets = $joinp->tabs->get($fields,$table,$AZZ);
                                        $this->sql = $joinp->tabs->sql;
                                        $this->SpreadP();
                                        $this->SpreadC();
                                        return $rets;
                                    }
                                    else if(is_array($INPUT["VALUES"])){
                                        for($ii=0;$ii<sizeof($INPUT["VALUES"]);$ii++){
                                             $fieldsx = $this->primary_key;
                                             if($this->isChild($INPUT["TABS"])){
                                                if($this->child[$INPUT["TABS"]]->GET_TAB()->isField($INPUT["FIELDS"]))
                                                    $fieldsx = $INPUT["FIELDS"];
                                                else $fieldsx = $this->child[$INPUT["TABS"]]->GET_TAB()->primary_key;
                                            }
                                            else if($this->isParent($INPUT["TABS"])){
                                                if($this->parents[$INPUT["TABS"]]->GET_TAB()->isField($INPUT["FIELDS"]))
                                                    $fieldsx = $INPUT["FIELDS"];
                                                else $fieldsx = $this->parents[$INPUT["TABS"]]->GET_TAB()->primary_key;
                                            }
                                             $joinp->Cond($INPUT["VALUES"][$ii],
                                                          $INPUT["TABS"],
                                                          $fieldsx,
                                                          $this->logic($INPUT,$ii),
                                                          $this->op($INPUT,$ii));
                                        }
                                        $this->range($INPUT,$joinp);
                                        $rets = $joinp->tabs->get($fields,$table,$AZZ);
                                        $this->sql = $joinp->tabs->sql;
                                        $this->SpreadP();
                                        $this->SpreadC();
                                        return $rets;
                                    }
                                    else {
                                        $joinp->Cond($INPUT["VALUES"],$INPUT["TABS"],$INPUT["FIELDS"]);
                                        $this->range($INPUT,$joinp);
                                        $rets = $joinp->tabs->get($fields,$table,$AZZ);
                                        $this->sql = $joinp->tabs->sql;
                                        $this->SpreadP();
                                        $this->SpreadC();
                                        return $rets;
                                    }
                                }
                                else if(isset($INPUT["VALUES"])){
                                    if(is_array($INPUT["VALUES"])){
                                        for($ii=0;$ii<sizeof($INPUT["VALUES"]);$ii++)
                                            $joinp->Cond($INPUT["VALUES"][$ii],
                                                         $INPUT["TABS"],
                                                         "",
                                                         $this->logic($INPUT,$ii),
                                                         $this->op($INPUT,$ii));
                                        $this->range($INPUT,$joinp);
                                        $rets = $joinp->tabs->get($fields,$table);
                                        $this->sql = $joinp->tabs->sql;
                                        $this->SpreadP();
                                        $this->SpreadC();
                                        return $rets;
                                    } else {
                                        if(($INPUT["TABS"]==$this->table_name)&&is_numeric($INPUT["VALUES"])){
                                           return $this->fetch($fields,$table,$INPUT["VALUES"]); 
                                        }  
                                        else {
                                           $joinp->Cond($INPUT["VALUES"],$INPUT["TABS"]);
                                           $this->range($INPUT,$joinp);
                                           $rets = $joinp->tabs->get($fields,$table);
                                           $this->sql = $joinp->tabs->sql;
                                           $this->SpreadP();
                                           $this->SpreadC();
                                           return $rets;
                                        }
                                    }
                                } else {
                                    return $this->_fetch_();
                                }
                            } else {
                                return $this->_fetch_();
                            }
                        }
                    }     
                }
                break;
            }
            case $INPUT instanceof PIP_Array:{
                break;
            }
            case is_numeric($INPUT):{
                $joinp = new JOINING($this);
                return $joinp->Cond(intval($INPUT))->tabs->get($fields,$table);
            }
            case NULL:{
                return $this->get();
            }
            default:{
                return $this->get();
            }
        }
        return new PIP_Array($this->SELECT_DATAS());
    }
    
    // a function with the same function like the above but with all fields
    public function _fetch_($INPUT = NULL){
        return $this->fetch("","",$INPUT);
    }
    
    // a function to initialize the query of the table 
    public function init(){
        $joinp = new JOINING($this);
        return $joinp->init()->tabs;
    } 
    // a public function to count number of childern of this element
    public function childNum(){
        return intval(sizeof($this->child)/2);
    }
    // a public function to count number of parents this one have
    public function parentsNum(){
        return intval(sizeof($this->parents)/2);
    }
    // a public function to spread the sql query along the parents and childern of this on
    public function SpreadP($sql = ""){
        if(!($sql=="")){
            $this->sql = $sql;
        }
        
        
        for($vv=0;$vv<$this->parentsNum();$vv++){
            $this->parents[$vv]->GET_TAB()->sql = $this->sql;
            $this->parents[$this->parents[$vv]->GET_TAB()->table_name]->GET_TAB()->sql = $this->sql;
            $this->parents[$vv]->GET_TAB()->SpreadP($this->sql);
            $this->parents[$this->parents[$vv]->GET_TAB()->table_name]->GET_TAB()->SpreadP($this->sql);
        }
    }
    public function SpreadC($sql = ""){
        
        
        if(!($sql=="")){
            $this->sql = $sql;
        }
        
        
        for($vv=0;$vv<$this->childNum();$vv++){
            $this->child[$vv]->GET_TAB()->sql = $this->sql;
            $this->child[$this->child[$vv]->GET_TAB()->table_name]->GET_TAB()->sql = $this->sql;
            $this->child[$vv]->GET_TAB()->SpreadC($this->sql);
            $this->child[$this->child[$vv]->GET_TAB()->table_name]->GET_TAB()->SpreadC($this->sql);
        }
    }
    // a function to make the initialization of the table in for providing less query than that of initial of the parent class WebApp
    public function initials($Query = "", $Engine = "", $CharSet = ""){
        $charset = "latin1";
        $eng = "InnoDB";
        if(!($CharSet=="")){
            $charset = $CharSet;
        }
        
        if(!($Engine=="")){
            $eng = $Engine;
        }
        $this->Structure = "CREATE TABLE IF NOT EXISTS `".$this->table_name."` (
                              `".$this->primary_key."` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT";
        if(!($Query=="")){
            $this->Structure = $this->Structure.", ".$Query;
        }
        $this->Structure = $this->Structure.") ENGINE=$eng DEFAULT CHARSET=$charset";
        $this->JS_ST();
        return $this;
    }
    // a function to empty the table for the restart
    public function empty(){
        if(isset($this->con["db"])){
            if($this->con["db"]!=""){
                $this->sql = "TRUNCATE `".$this->con["db"]."`.`".$this->table_name."`";
            } else {
                $this->sql = "TRUNCATE `".$this->table_name."`";
            }
            $this->conn = $this->open();
            $result = mysqli_query($this->conn,$this->sql);
            if($result){
                $this->message = " warning : the $this->table_name is now empty";
                $this->close();
                return true;
            } else {
                $this->message = " failed to empty the table << $this->table_name >>".mysqli_error($this->conn);
                return false;
            }
        } else {
            $this->con["db"] = "";
            return $this->empty();
        }
    }
    // a function to add a field to the table where fieldname is the name of the field or JS_STRUCTURE of the field and type is the type of the field can be a direct string if the fieldname is a string and a string or JS_STRUCTURE in case of fieldname is the JS_STRUCTURE(in this case it will be considered as the fields to be added after not type with size), it will be the last column if not provided, the after parameter will be the after column in the MySql Clause. 
    public function push($fieldsName, $type = "VARCHAR(50)", $after = "") {
        if($fieldsName instanceof JS_STRUCTURE){
            for($ii=0;$ii<$this->Width();$ii++){
                if($this->JS_st[$ii]->name==$fieldsName->name){
                    $this->message = " the structure($fieldsName->name) given already exist";
                    return false;
                }
            }
            $this->sql = "ALTER TABLE `".$this->table_name."` 
                                ADD `$fieldsName->name` ".$fieldsName->struct()["type"]."(".$fieldsName->struct()["size"].")
                                NOT NULL AFTER";
            if($type instanceof JS_STRUCTURE){
                $included = false;
                for($ii=0;$ii<$this->Width();$ii++){
                    if($this->JS_st[$ii]->name==$type->name){
                        $included = true;
                        break;
                    }    
                }
                if($included){
                    $this->sql = $this->sql." `$type->name`";
                } else {
                    return $this->push($fieldsName);
                }
            } 
            else if($type=="VARCHAR(50)"){
                $this->sql = $this->sql." `".$this->JS_st[$this->Width()-1]->name."`";
            } 
            else if(is_string($type)){
                $included = false;
                for($ii=0;$ii<$this->Width();$ii++){
                    if($this->JS_st[$ii]->name==$type){
                        $included = true;
                        break;
                    }
                }
                if($included){
                    $contains = false;
                    for($ii=0;$ii<array_keys(JS_STRUCTURE::POSSIBLE_DT);$ii++)
                        if(!contains_arr(JS_STRUCTURE::POSSIBLE_DT[array_keys(JS_STRUCTURE::POSSIBLE_DT)[$ii]],
                                         admin::ST_EXTRACT($type)["type"])){
                            $contains = true;
                            break;
                        }
                    if($contains)
                        $this->sql = $this->sql." `$type`";
                    else {
                        $this->message = "the datatype given in not supported";
                        return false;
                    }
                } else {
                    return $this->push($fieldsName);
                }
            } 
            else {
                return $this->push($fieldsName);
            }
            array_push($this->JS_st,$fieldsName);
            $this->JS_st[$fieldsName->name] = $fieldsName;
        } 
        else if(is_string($fieldsName)){
            for($ii=0;$ii<$this->Width();$ii++){
                if($this->JS_st[$ii]->name==$fieldsName){
                    $this->message = " the structure($fieldsName) given already exist";
                    return false;
                }
            }
            $contains = false;
            for($ii=0;$ii<array_keys(JS_STRUCTURE::POSSIBLE_DT);$ii++)
                if(!contains_arr(JS_STRUCTURE::POSSIBLE_DT[array_keys(JS_STRUCTURE::POSSIBLE_DT)[$ii]],
                                 admin::ST_EXTRACT($type)["type"])){
                    $contains = true;
                    break;
                }
            
            if(!$contains) {
                $this->message = "the datatype given in not supported";
                return false;
            }
            $this->sql = "ALTER TABLE `".$this->table_name."` 
                                ADD `$fieldsName` $type
                                NOT NULL AFTER";
            if($after==""){
                $this->sql = $this->sql." `".$this->JS_st[$this->Width()-1]->name."`";
            } 
            else if($after instanceof JS_STRUCTURE){
                $exist = false;
                for($ii=0;$ii<$this->Width();$ii++){
                    if($this->JS_st[$ii]->name==$after->name){
                        $exist = true;
                        break;
                    }   
                }
                if($exist){
                    $this->sql = $this->sql." `$after->name`";
                } else {
                    $this->message = " the field provided ($after->name) does'nt exist in the table";
                    return $this->push($fieldsName,$type);
                }
            }
            else if(is_string($after)){
                $exist = false;
                for($ii=0;$ii<$this->Width();$ii++){
                    if($this->JS_st[$ii]->name==$after){
                        $exist = true;
                        break;
                    }   
                }
                if($exist){
                    $this->sql = $this->sql." `$after`";
                } else {
                    $this->message = " the field provided ($after) does'nt exist in the table";
                    return $this->push($fieldsName,$type);
                }
            }
            else {
                return $this->push($fieldsName,$type);
            }
            
            $new_js_st = JS_ST($fieldsName)
                               ->Stype(admin::ST_EXTRACT($type)["type"])
                               ->Ssize(admin::ST_EXTRACT($type)["size"])
                               ->Sclasses($fieldsName);
            
            array_push($this->JS_st,$new_js_st);
            $this->JS_st[$fieldsName] = $new_js_st;
        }
        else {
            $this->message = "Invalid input to add a field to the ($this->table_name)";
            return false;
        }
        $this->conn = $this->open();
        $result = mysqli_query($this->conn,$this->sql);
        if($result){
           $this->message = "new column was added successfuly!";
           $this->close();
           $this->conn = 0;
           return true; 
        } 
        else {
           $this->message = "failed to add another field :: ".mysqli_error($this->conn);
           $this->close();
           $this->conn = 0; 
           return false;
        }
    }
    // a function to display some basic info of the table
    public function Info($what = 0){
        if($what==0){
            $children = array();
            for($ii=0;$ii<$this->childNum();$ii++){
                array_push($children,["table"=>$this->child[$ii]->GET_TAB()->table_name,"foreign_key"=>$this->child[$ii]->GET_FORE()]);
            }

            $parents = array();

            for($ii=0;$ii<$this->parentsNum();$ii++){
                array_push($parents,["table"=>$this->parents[$ii]->GET_TAB()->table_name,"foreign_key"=>$this->parents[$ii]->GET_FORE()]);
            }
            $n_conns = -10000;
            $n_threads = -1000;
            $this->conn = $this->open();
            if($results = mysqli_query($this->conn,"SHOW FULL PROCESSLIST")){
                $n_conns = mysqli_num_rows($results) - 1;
                mysqli_free_result($results);
            } else {
                $this->message = mysqli_error($this->conn);
            }
            
            if($results = mysqli_query($this->conn,"SHOW STATUS WHERE `variable_name` = 'Threads_connected'")){
                if(mysqli_num_rows($results)){
                    $n_threads = mysqli_fetch_array($results,MYSQLI_BOTH)["Value"];
                } else {
                    $n_threads = 0;
                }
                mysqli_free_result($results);
            } else {
                $this->message = mysqli_error($this->conn);
            }
            $this->close();
            
            $outPut = [
                "name"=>$this->table_name,
                "primaryKey"=>$this->primary_key,
                "lastQuery"=>$this->sql,
                "childreNumber"=>$this->childNum(),
                "children"=>$children,
                "parentsNumber"=>$this->parentsNum(),
                "parents"=>$parents,
                "structure"=>$this->Structure,
                "ActiveConnections"=>$n_conns,
                "Thread connected"=>$n_threads,
                "message"=>$this->message,
                "JS_ST"=>$this->JS_st,
                "ST_LEN"=>$this->ST_LEN()
                
            ];
            echo "<pre>".print_r($outPut,1)."</pre>";
        } 
        else if($what==1){
            if($this->conn instanceof mysqli){
                echo "<pre>".print_r($this->conn,1)."</pre>";
            } else {
                $this->conn = $this->open();
                echo "<pre>".print_r($this->conn,1)."</pre>";
                $this->close();
             }
        } 
        else if($what==2){
            echo "<pre>".print_r($this->con,1)."</pre>";
            
        } 
        else {
            $this->Info(0);
        }
    }
    // a function to analyze the Structure of the table the return the structrure of the table in form of JS_STRUCTURE
    private function JS_ST(){
        $refWords = "CREATE TABLE IF NOT EXISTS";
        $pointerDict = [
            "tableName"=>[
                "status"=>false,
                "started"=>false,
                "ended"=>false,
                "contents"=>""
            ],
            "struct"=>[
               "primaryKey"=>[
                    "status"=>false,
                    "started"=>false,
                    "ended"=>false,
                    "contents"=>""
                ],
               "otherFields"=>[
                    "status"=>false,
                    "started"=>false,
                    "ended"=>false,
                    "count"=>0,
                    "temp"=>"",
                    "contents"=>array()
                ],
                "status"=>false
            ],
            "engine"=>[
                    "status"=>false,
                    "started"=>false,
                    "ended"=>false,
                    "contents"=>""
                ],
            "charset"=>[
                    "status"=>false,
                    "started"=>false,
                    "ended"=>false,
                    "contents"=>""
                ]
        ];
        //echo substr($this->Structure,0,26);
        if($this->ST_LEN()==0){
            $this->message = "the structure of the table is not yet defined";
            return false;
        } else if(!($refWords==substr($this->Structure,0,26))){
            $this->message = "the structure of the table is not valid!!";
            return false;
        }
        else {
            for($ii=26;$ii<$this->ST_LEN();$ii++){
                if($this->ST_CHAR($ii)=="_1"){
                    break;
                } else switch($this->ST_LOC($pointerDict)){
                        case 10:{
                           if($pointerDict["tableName"]["started"]){
                               if(!($this->ST_CHAR($ii)=="`"))
                                    $pointerDict["tableName"]["contents"] = $pointerDict["tableName"]["contents"].$this->ST_CHAR($ii);
                               else {
                                   $pointerDict["tableName"]["ended"] = true;
                                   $pointerDict["tableName"]["started"] = false;
                               }
                           }
                           else if($pointerDict["tableName"]["ended"]){
                               if($this->ST_CHAR($ii)=="("){
                                   $pointerDict["struct"]["status"] = true;
                                   $pointerDict["tableName"]["status"] = false;
                                   //echo $this->ST_LOC($pointerDict)."..";
                               } else {
                                   $pointerDict["tableName"]["ended"] = true;
                                   $pointerDict["tableName"]["started"] = false;
                               }
                           }
                           else {
                               if($this->ST_CHAR($ii)=="`"){
                                   $pointerDict["tableName"]["started"] = true;
                                   $pointerDict["tableName"]["ended"] = false;
                               } else {
                                 $this->message = "the structure of the table is not valid!!( missing name start)";
                                 return false;
                               }
                           }
                           break; 
                        }
                        case 21:{
                            if($pointerDict["struct"]["primaryKey"]["started"]){
                                if($this->ST_CHAR($ii)=="`"){
                                    $pointerDict["struct"]["primaryKey"]["started"] = false;
                                    $pointerDict["struct"]["primaryKey"]["ended"] = true;
                                    $this->JS_st[0]
                                        ->Stype($this->ST_TYPE($this->ST_CHAR($ii,
                                                                              $this->ST_LEN())
                                                              ))
                                        ->Ssize($this->ST_SIZE($this->ST_CHAR($ii,
                                                                             $this->ST_LEN())
                                                             ));
                                    
                                    $this->JS_st[$this->primary_key]
                                        ->Stype($this->ST_TYPE($this->ST_CHAR($ii,
                                                                              $this->ST_LEN())
                                                              ))
                                        ->Ssize($this->ST_SIZE($this->ST_CHAR($ii,
                                                                             $this->ST_LEN())
                                                             ));
                                    
                                } else {
                                    $pointerDict["struct"]["primaryKey"]["contents"] = 
                                    $pointerDict["struct"]["primaryKey"]["contents"].$this->ST_CHAR($ii);
                                    
                                }
                            } else if($pointerDict["struct"]["primaryKey"]["ended"]){
                                if($this->ST_CHAR($ii)==","){
                                  $pointerDict["struct"]["primaryKey"]["status"] = false;
                                  $pointerDict["struct"]["otherFields"]["status"] = true;   
                                }
                            } else {
                                if($this->ST_CHAR($ii)=="`"){
                                    $pointerDict["struct"]["primaryKey"]["started"] = true;
                                    $pointerDict["struct"]["primaryKey"]["ended"] = false;
                                }
                                else if($this->ST_CHAR($ii)==" "){
                                    continue 2;
                                }
                                else {
                                    $this->message = "the structure of the table is not valid!!( missing primary key start )";
                                    return false;
                                }
                            }
                            break;
                        }
                        case 22:{
                               if($this->ST_CHAR($ii)=="`"){
                                   if(!$pointerDict["struct"]["otherFields"]["started"]){
                                      $pointerDict["struct"]["otherFields"]["started"] = true;
                                   } else {
                                       if($this->ST_CHAR($ii+1)==" "){
                                           $pointerDict["struct"]["otherFields"]["temp"] = 
                                               preg_replace('/\s+/',' ',$pointerDict["struct"]["otherFields"]["temp"]);
                                               if(substr($pointerDict["struct"]["otherFields"]["temp"],0,1)==" ")
                                                   $pointerDict["struct"]["otherFields"]["temp"] = 
                                                   substr($pointerDict["struct"]["otherFields"]["temp"],1,
                                                          strlen($pointerDict["struct"]["otherFields"]["temp"]));
                                           array_push($pointerDict["struct"]["otherFields"]["contents"],
                                                      $pointerDict["struct"]["otherFields"]["temp"]);
                                           
                                           array_push($this->JS_st,
                                                      JS_ST($pointerDict["struct"]["otherFields"]["temp"])
                                                            ->Stype($this->ST_TYPE($this->ST_CHAR($ii,$this->ST_LEN())))
                                                            ->Ssize($this->ST_SIZE($this->ST_CHAR($ii,$this->ST_LEN())))
                                                            ->Sclasses($pointerDict["struct"]["otherFields"]["temp"])
                                                     );
                                           $this->JS_st[$pointerDict["struct"]["otherFields"]["temp"]] = 
                                               JS_ST($pointerDict["struct"]["otherFields"]["temp"])
                                                            ->Stype($this->ST_TYPE($this->ST_CHAR($ii,$this->ST_LEN())))
                                                            ->Ssize($this->ST_SIZE($this->ST_CHAR($ii,$this->ST_LEN())))
                                                            ->Sclasses($pointerDict["struct"]["otherFields"]["temp"]);
                                           $pointerDict["struct"]["otherFields"]["temp"] = "";
                                           $pointerDict["struct"]["otherFields"]["started"] = false;
                                           $pointerDict["struct"]["otherFields"]["ended"] = true;
                                       }
                                   }
                               } else if($this->ST_CHAR($ii+1)==","){
                                   $pointerDict["struct"]["otherFields"]["count"]++;
                                   $pointerDict["struct"]["otherFields"]["ended"] = false;
                               } else if($this->ST_CHAR($ii)=="="){
                                   $pointerDict["struct"]["otherFields"]["ended"] = false;
                                   $pointerDict["struct"]["otherFields"]["started"] = false;
                                   $pointerDict["struct"]["primaryKey"]["status"] = false;
                                   $pointerDict["struct"]["otherFields"]["status"] = false;
                                   $pointerDict["struct"]["status"] = false;
                                   $pointerDict["engine"]["status"] = true;
                                   $ii = $ii-8;
                                   
                               } else  {
                                   if((!$pointerDict["struct"]["otherFields"]["ended"])&&
                                      ($this->ST_CHAR($ii)!=",")&&
                                      ($this->ST_CHAR($ii)!=" "))
                                       $pointerDict["struct"]["otherFields"]["temp"] .= $this->ST_CHAR($ii);
                               }
                            break;
                        }
                        case 30:{
                            if($pointerDict["engine"]["started"]){
                                if($this->ST_CHAR($ii)==" "){
                                    $pointerDict["engine"]["status"] = false;
                                    $pointerDict["engine"]["started"] = false;
                                    $pointerDict["engine"]["ended"] = true;
                                    $pointerDict["charset"]["status"] = true;
                                }
                                else {
                                    $pointerDict["engine"]["contents"] .= $this->ST_CHAR($ii);
                                }
                                
                            }
                            if(!$pointerDict["engine"]["ended"])
                                if($this->ST_CHAR($ii)=="=")
                                   $pointerDict["engine"]["started"] = true;
                            break;
                        }
                        case 40:{
                            if($pointerDict["charset"]["started"]){
                                if(($this->ST_CHAR($ii)==")")||($this->ST_LEN()==$ii)){
                                    $pointerDict["charset"]["status"] = false;
                                    $pointerDict["charset"]["started"] = false;
                                    $pointerDict["charset"]["ended"] = true;
                                } else {
                                    $pointerDict["charset"]["contents"] .= $this->ST_CHAR($ii);
                                }
                            }   
                            if(!$pointerDict["charset"]["ended"])   
                                if($this->ST_CHAR($ii)=="=")
                                    $pointerDict["charset"]["started"] = true;
                            
                            break;
                        }
                        case -100:{
                            $pointerDict["tableName"]["status"] = true;
                            break;
                        }
                        case -200:{
                            $pointerDict["struct"]["primaryKey"]["status"] = true;
                            break;
                        }
                        case -700:{
                            $this->message = "invalid dictionary!!";
                        }
                        default:{
                            break;
                        }
                    }
            }
            
            if($pointerDict["tableName"]["contents"]==$this->table_name){
                $this->ENGINE = $pointerDict["engine"]["contents"];
                $this->CHARSET = $pointerDict["charset"]["contents"];
                //$this->save("structure",$this->Structure);
                $all = "";
                for($ii=0;$ii<$this->Width();$ii++){
                   $all .=  $this->JS_st[$ii]->tr();
                }
                //$this->save("structure_php",$all);
                return true;
            }
            else {
                $this->message = " table name provided is not matching with the one given in the structure of the table ";
                return false;
            }
            
        }
    }
    // a function to check if the given field is listed in the JS_STRUCTURE of the table structure
    public function isField($name){
        for($ii=0;$ii<sizeof($this->JS_st);$ii++){
            if($this->JS_st[$ii]->name==$name) return true;
        }
        return false;
    }
    // a function  to add some content to the external files to archive them
    private function save($file_index,$data){
        if(isset($this->backup_files[$file_index])){
            if (!$fp = @fopen($this->backup.$this->backup_files[$file_index], 'w')) {
               throw new Exception('could not open the file.');
               return false;
            }
            if (!@fwrite($fp,$data)) {
               throw new Exception('could not write to the file.');
                return false;
            }
            if (!@fclose($fp)) {
                throw new Exception('could not close the file.');
                return false;
            }
            return true;
        } else {
            $this->message = "the index $file_index given was not defined";
            return false;
        }
        
    }
    // a function to indicate where the structure pointer is located 
    private function ST_LOC($dict){
        if(isset($dict["tableName"])&&isset($dict["struct"])&&isset($dict["engine"])&&isset($dict["charset"])){
            if($dict["tableName"]["status"]){
                return 10;
            } else if($dict["struct"]["status"]){
                if($dict["struct"]["primaryKey"]["status"]){
                    return 21;
                } else if($dict["struct"]["otherFields"]["status"]){
                    return 22;
                } else {
                    return -200;
                }
            } else if($dict["engine"]["status"]){
                return 30;
            } else if($dict["charset"]["status"]){
                return 40;
            } else {
                return -100;
            }
        } else {
            return -700;
        }
    }
    // a private function to search for the the datatype with the given string location
    private function ST_TYPE($str){
        $type = "";
        for($ii=0;$ii<strlen($str);$ii++){
            if(substr($str,$ii,1)=="(")
                break;
            else if(substr($str,$ii,4)==" NOT")
                break;
            else if((substr($str,$ii,1)=="`")||(substr($str,$ii,1)==" "))
                 continue;
            else $type = $type.substr($str,$ii,1);
        }
        for($ii=0;$ii<sizeof(JS_STRUCTURE::POSSIBLE_DT);$ii++){
            for($iii=0;$iii<sizeof(JS_STRUCTURE::POSSIBLE_DT[array_keys(JS_STRUCTURE::POSSIBLE_DT)[$ii]]);$iii++){
                if(JS_STRUCTURE::POSSIBLE_DT[array_keys(JS_STRUCTURE::POSSIBLE_DT)[$ii]][$iii]["name"]==strtoupper($type))
                    return JS_STRUCTURE::POSSIBLE_DT[array_keys(JS_STRUCTURE::POSSIBLE_DT)[$ii]][$iii]["name"];
            }
        }
        return "undefined";
    }
    // a private function to find the size of the datatype specified
    private function ST_SIZE($str){
        $start = false;
        $size = "";
        for($ii=0;$ii<strlen($str);$ii++){
            if(substr($str,$ii,1)==",")
                break;
            if(substr($str,$ii,1)=="("){
                if(substr($str,$ii+1,1)==")"){
                    break;
                } else {
                   $start = true; 
                }
            } else if($start){
                if(substr($str,$ii,1)==")")
                    break;
                else $size = $size.substr($str,$ii,1);
            }
        }
        return $size;
    }
    // a function to read the length of the structure given of the table
    private function ST_LEN(){
        return strlen($this->Structure);
    }
    // a function to return an index of the stucture of the table definition
    private function ST_CHAR($index = 0, $len = 1){
        if($this->ST_LEN()){
            if($index){
                 if($index<$this->ST_LEN()){
                     $this->message = "The index given was found success".substr($this->Structure,$index,($index+1));
                     return substr($this->Structure,$index,$len);
                 } else {
                     $this->message = "The index given is outof bound of the table structure";
                     return "_1";
                 }
            } else {
                return substr($this->Structure,0,1);
            }
        } else {
            $this->message = "you are trying to use the empty table strucure";
            return "_";
        } 
    }
    
    // a private function to extract the datatype from the given type 
    public static function ST_EXTRACT($type){
        return [
            "type"=>"VARCHAR",
            "size"=>50
        ];
    }
    // a function to return the width of the table in form of the column number
    public function Width(){
        if(sizeof($this->JS_st)>1)
            return (sizeof($this->JS_st)/2);
        else if(sizeof($this->JS_st)==1)
            return 1;
        else return 0;
    }
    // a function to return the height of the table in form of row or record number
    public function Height(){
        return $this->counts();
    }
}
//the class to monitor the process of JOINING Tables
class JOINING {
   // the sql query to be reurned while creating this object
   public $tabs;
   // the constructor to keep for initializer where $table is a full admin
   function __construct($table){
       if($table instanceof admin)
         $this->tabs = $table;
       else throw new Exception("Error :: the input of the JOINING constructor must be an insctance of admin class :: ");
   }
   // a function to initialize the object if there was another call of the Query
   public function init(){
       $this->tabs->sql = "SELECT * FROM `".$this->tabs->table_name."`";
       return $this;
   }
   // a function to join with the child specified $childs can be a string or a admin object and $type is the type of join Like INNER, LEFT,...
   function JOINC(&$childs,$type){
       $foreign_tab = $childs;
       if($childs instanceof admin)
           $foreign_tab = $childs->table_name;
       if($this->tabs->isChild($foreign_tab)){
           $this->tabs->sql = $this->tabs->sql." $type JOIN `".
               $this->tabs->child[$foreign_tab]->GET_TAB()->table_name."` ON `".
               $this->tabs->table_name."`.`".$this->tabs->primary_key."` = `".
               $this->tabs->child[$foreign_tab]->GET_TAB()->table_name."`.`".
               $this->tabs->child[$foreign_tab]->GET_FORE()."` ";
           $this->tabs->newChild($this->tabs->child[$foreign_tab]->GET_TAB()->table_name);
           $this->tabs->child[$foreign_tab]->GET_TAB()->newParent($this->tabs->table_name);
           $this->tabs->SpreadC($this->tabs->sql);
           $this->tabs->SpreadP($this->tabs->sql);
       }
       $childs->sql = $this->tabs->sql;
       return $this;
   }
   // a function to join with the parent specified $parent can be a string or a admin object and $type is the same as the above JOINC
   function JOINP(&$parent,$type){
       $foreign_tab = $parent;
       if($parent instanceof admin)
           $foreign_tab = $parent->table_name;
       if($this->tabs->isParent($foreign_tab)){
            $this->tabs->sql = $this->tabs->sql." $type JOIN `".
               $this->tabs->parents[$foreign_tab]->GET_TAB()->table_name."` ON `".
               $this->tabs->table_name."`.`".$this->tabs->parents[$foreign_tab]->GET_FORE()."` = `".
               $this->tabs->parents[$foreign_tab]->GET_TAB()->table_name."`.`".
               $this->tabs->parents[$foreign_tab]->GET_TAB()->primary_key."` ";
            $this->tabs->newParent($this->tabs->parents[$foreign_tab]->GET_TAB()->table_name);
            $this->tabs->parents[$foreign_tab]->GET_TAB()->newChild($this->tabs->table_name);
            $this->tabs->SpreadC($this->tabs->sql);
            $this->tabs->SpreadP($this->tabs->sql);
       }
       $parent->sql = $this->tabs->sql;
       return $this;
   }
    
   // a function to join table with the copy of child sql
    
    function JOINPP(&$parent,&$tab,$type){
        $foreign_tab = $parent;
        if($tab instanceof admin){
            $foreign_tab = $parent->table_name;
            if($this->tabs->isParent($foreign_tab)){
                $this->tabs->sql = $tab->sql;
                $this->tabs->sql = $this->tabs->sql." $type JOIN `".
                $this->tabs->parents[$foreign_tab]->GET_TAB()->table_name."` ON `".
                $this->tabs->table_name."`.`".$this->tabs->parents[$foreign_tab]->GET_FORE()."` = `".
                $this->tabs->parents[$foreign_tab]->GET_TAB()->table_name."`.`".
                $this->tabs->parents[$foreign_tab]->GET_TAB()->primary_key."` ";
                $this->tabs->newChild($this->tabs->child[$foreign_tab]->GET_TAB()->table_name);
                $this->tabs->child[$foreign_tab]->GET_TAB()->newParent($this->tabs->table_name);
                $tab->SpreadP($this->tabs->sql); 
                $tab->SpreadC($this->tabs->sql);
            } else if($this->tabs->isChild($foreign_tab)){
                $this->tabs->sql = $this->tabs->sql." $type JOIN `".
                $this->tabs->child[$foreign_tab]->GET_TAB()->table_name."` ON `".
                $this->tabs->table_name."`.`".$this->tabs->primary_key."` = `".
                $this->tabs->child[$foreign_tab]->GET_TAB()->table_name."`.`".
                $this->tabs->child[$foreign_tab]->GET_FORE()."` ";
                $this->tabs->newParent($this->tabs->parents[$foreign_tab]->table_name);
                $this->tabs->parents[$foreign_tab]->GET_TAB()->newChild($this->tabs->table_name);
                $tab->SpreadC($this->tabs->sql); 
                $tab->SpreadP($this->tabs->sql);
            }
          $this->tabs->SpreadC($this->tabs->sql);
          $this->tabs->SpreadP($this->tabs->sql);
        }
        return $this;
    }
    
   // a public function to add some conditions to the query where $fe
    
    public function Cond($val,$tab = "",$fields = "",$bool = "", $sign = "="){
        $tabFields = $this->tableField($tab,$fields);
        $tableName = $tabFields["tabs"];
        $Fields = $tabFields["fields"];
        
        if(($tableName==NULL)||($Fields==NULL)){
            return $this;
        }
        
        if($bool==""){
            $bool = "WHERE";
            if(strpos($this->tabs->sql,"WHERE"))
                $bool = "AND";
        } else if(!strpos($this->tabs->sql,"WHERE"))
             $bool = "WHERE";
        
        if($sign==""){
            $sign = "=";
        }
        
        $this->tabs->sql = $this->tabs->sql." $bool `$tableName`.`$Fields` $sign '$val'";
        return $this;
    }
    
   // a public function to add some limts on the data to fetch in sql 
    public function range($tab = "", $fields = "",$order = 0,$start = -5,$end = -5){
        $tabFields = $this->tableField($tab,$fields);
        $tableName = $tabFields["tabs"];
        $Fields = $tabFields["fields"];
        
        if(($tableName==NULL)||($Fields==NULL)){
            return $this;
        }
        
       if($order>0)
           $order = "DESC";
       else $order = "ASC";
        
       if($start < -5){
           $start = 1; 
       }
       $LIMITS = "";
       if($end<0){
           $LIMITS = $start;
       } else {
           $LIMITS = $start.",".$end;
       }
        
       $this->tabs->sql = $this->tabs->sql." ORDER BY `$tableName`.`$Fields` $order LIMIT $LIMITS ";
       return $this;
           
    }
    
   // a private function to return the real names of table and fields
    private function tableField($tab,$fields){
        $tableName = $tab;
        $Fields = $fields;
        if($tab==""){
            $tableName = $this->tabs->table_name;
            if(($fields=="")||
               (!$this->tabs->isField($fields))){
                $Fields = $this->tabs->primary_key;
            }
        } 
        else if($tab instanceof admin){
            if($this->tabs->isChild($tab->table_name)){
                $tableName = $tab->table_name;
                if(($fields=="")||
                   (!$this->tabs->child[$tab->table_name]->GET_TAB()->isField($fields))){
                    $Fields = $tab->primary_key;
                }
            } else if($this->tabs->isParent($tab->table_name)){
                $tableName = $tab->table_name;
                if(($fields=="")||
                   (!$this->tabs->parents[$tab->table_name]->GET_TAB()->isField($fields))){
                    $Fields = $tab->primary_key;
                }
            } else if($tab->table_name==$this->tabs->table_name){
                $tableName = $tab->table_name;
                if(($fields=="")||
                   (!$this->tabs->isField($fields))){
                    $Fields = $tab->primary_key;
                }
            } else {
                $tableName = NULL;
                $Fields = NULL;
                //return $this;
            }
        } 
        else {
            if($this->tabs->isChild($tab)){
                $tableName = $tab;
                if(($fields=="")||
                   (!$this->tabs->child[$tab]->GET_TAB()->isField($fields))){
                    $Fields = $this->tabs->child[$tab]->GET_TAB()->primary_key;
                }
            } else if($this->tabs->isParent($tab)){
                $tableName = $tab;
                if(($fields=="")||
                   (!$this->tabs->parents[$tab]->GET_TAB()->isField($fields))){
                    $Fields = $this->tabs->parents[$tab]->GET_TAB()->primary_key;
                }
            } else if($this->tabs->table_name==$tab){
                $tableName = $tab;
                if(($fields=="")||
                   (!$this->tabs->isField($fields))){
                    $Fields = $this->tabs->primary_key;
                }
            } else {
                $tableName = NULL;
                $Fields = NULL;
                //return $this;
            }
        }
        return [
            "tabs"=>$tableName,
            "fields"=>$Fields
        ];
    }
    
}
// the class to keep values of the encrypted data for precision purpose
class NUM_CHARS{
    function __construct($str){
        $this->str = $str;
    }
}
/* THE CLASS WHICH IS DEDICATED FOR STRING ENCRYPTION AND DECRYPTION process */
class pipEncrypt{
    // a hash table of number where it will be used in converting numbers and position of strings
    private  $num_dictionary = ["6","8","1","0","7","3","4","9","2","5"];
    // a hash table of character where it will be used as a dictionary
    private $alpha_dictionary = ["X","g","m","U","p","T","A","Z","Q","n","B","V","c","F","d","i","L","H","w","Y","S","K","j","e","O","r",
     "R","o","E","J","k","W","h","l","I","D","f","C","v","b","N","q","z","a","t","y","u","M","G","s","P","x"];
    // coresponding hash table in alphabetical order
    private $alpha_order = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
     "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    function __construct($original,$keyNumber){
 		$this->original = $original;
 		$this->encrypted = "";
 		$this->keys = $keyNumber;
        $this->dots = "";
        $this->bracket = "";
 	}
    // private function just to minimize BracketDatas into single
    private function bracketSingle($BracketString){
                $allValues = array();
                $Closing_bracket = "";
                $Opening_bracket = "";
                $LEVELS = 0;
        for($xcxc=0;$xcxc<sizeof($BracketString);$xcxc++){
        while(substr($BracketString[$xcxc],$LEVELS,1)=="("){
                    $Opening_bracket = $Opening_bracket."(";
                    $Closing_bracket = $Closing_bracket.")";
                    $LEVELS++;
                }
             $singleStr = "";
             for($v=0;$v<strlen($BracketString[$xcxc]);$v++){
                 $this_array = $v+$LEVELS;
                 $insiders = array();
                  if((substr($BracketString[$xcxc],$v,$LEVELS)==$Opening_bracket)){
                    while(!(substr($BracketString[$xcxc],$this_array,$LEVELS)==$Closing_bracket)){
                       $singleStr = $singleStr.substr($BracketString[$xcxc],$this_array,1);
                        $this_array++;
                   }
                      for($xx=0;$xx<($LEVELS-1);$xx++){
                          $singleStr = "(".$singleStr.")";
                      }
                      
                      array_push($allValues,$singleStr);
                      $singleStr = "";
                  } 
               }
            }
             return $allValues;
    }
    
    // the function that will return an array from datas inside a bracket like (98)(78)(7889) into [98][78][7889]
    public function bracketDatas(){
            $checked = array(); 
            $finalStr = "";
            
            $LEVELS = 0;
            $POINTER = 0;
            $number_of_opening = 0;
            $number_of_closing = 0;
            for($v=0;$v<strlen($this->original);$v++){
                if(substr($this->original,$v,1)=="(")
                  $number_of_opening++;
                else if(substr($this->original,$v,1)==")")
                  $number_of_closing++;
            }
            
            if($number_of_closing==$number_of_opening){
                $Closing_bracket = "";
                $Opening_bracket = "";
                $LEVELS = 0;
                while(substr($this->original,$LEVELS,1)=="("){
                    $Opening_bracket = $Opening_bracket."(";
                    $Closing_bracket = $Closing_bracket.")";
                    $LEVELS++;
                }
                
                $OriginalDatas = [$this->original];
                while(substr($OriginalDatas[0],0,1)=="("){
                    
                    $OriginalDatas = $this->bracketSingle($OriginalDatas);
                }
                
               $checked = $OriginalDatas; 
            } else {
                array_push($checked,"ERROR! THE BRACKET DATA GIVEN IS NOT VALID");
            }

              return $checked;
          }
    // the function that will return an equivalent character form a number specified of 
    private function to_chars($NUMBS){
        $newv = intval($NUMBS)+intval($this->keys);
        $newv = "$newv";
        $sub_strs = array();
        if(strlen($newv)<2){
            array_push($sub_strs,$newv);
        } else {
            for($ii=0;$ii<strlen($newv);){
                
                $limit = 2;
                if(strlen($newv)%2==0){
                    $limit = 2;
                } else if(intval(strlen($newv)-$ii)==1){
                    $limit = 1;
                }
                array_push($sub_strs,substr($newv,$ii,$limit));
                $ii+=2;
            }
        }
        $newv = "";
        for($ii=0;$ii<sizeof($sub_strs);$ii++){
            
            if(substr($sub_strs[$ii],0,1)=="0"){
                $sub_strs[$ii] = "-".$sub_strs[$ii];
            }   
            else if($sub_strs[$ii]=="99"){
                $sub_strs[$ii] = ".".$sub_strs[$ii];
            } else {
                $sub_strs[$ii] = chr($sub_strs[$ii]);
            }
            $newv .= $sub_strs[$ii];
        }
        
        //echo "<pre>".print_r($sub_strs,1)."</pre>";
        
        return $newv;
    }
    
    private function to_numbs($CHARS){
        $nums = array();
        $all = "";
        for($ii=0;$ii<strlen($CHARS);$ii++){
            $chars = substr($CHARS,$ii,1);
            array_push($nums,ord($chars));
            $all = $all.ord($chars);
        }
        
        $all_len = strlen($all)-5;
        
        $hints = substr($all,$all_len);
        
        if($hints=="65757"){
            echo "(".$hints.")";
        }
        
        
        
        
        $all = intval($all) ;//- intval($this->keys);
        //print_r($nums);
        
        return $all;
    }
    
    private function toCHARS($NUMBS){
            $NEWV = "$NUMBS";
            $len = strlen($NEWV);
            $RETS = "";
            for($var=0;$var<$len;$var++){
                 $temp = intval(substr($NEWV,strlen($NEWV)-1)) + $this->keys;
                 $RETS .= chr($temp);
                 $NEWV = substr_replace($NEWV,"",strlen($NEWV)-1);
               }
              return strrev($RETS);
            }
    // the function that will return an equivalent number form a characters specified of 
    private function tonumbs($chars){
            $len = strlen($chars);
            $NEWV = $chars;
            $RETS = "";
            for($var=0;$var<$len;$var++){
                 $temp = intval(ord(substr($NEWV,strlen($NEWV)-1))) -  $this->keys;
                 //echo $temp.">";
                 $RETS .= $temp;
                 $NEWV = substr_replace($NEWV,"",strlen($NEWV)-1);
               }
              return strrev($RETS);
            }
    // the function that will return an encrypted string of hexadecimal number the argument of an array
    public function finalstr($prod){
        $finalStr = "";
        for($ii=0;$ii<sizeof($prod);$ii++){
          $finalStr = $finalStr."trnb".$this->toCHARS($prod[$ii]); 
        }
        return bin2hex($finalStr);
    }
    public function num_to_hex($arr,$key,$size = 0,$separation_char = "-"){
        $finalStr = "";
        if(is_array($arr)){
            for($ii=0;$ii<sizeof($arr);$ii++){
                $finalStr = $finalStr.$key.$this->to_chars($arr[$ii]);
            }
        } else {
           $finalStr = $this->to_chars($arr[$ii]).$key;
        }
        if($size==0)
            return bin2hex($finalStr);
        else return $this->separation(bin2hex($finalStr),$size,$separation_char);
    }
    // a private function to separate the encrypted indexes in forms of 0000-0000-0000-0000
    private function separation($str,$size,$separation_char = "-"){
        $new_str = "";
        for($ii=0;$ii<strlen($str);$ii++){
            $char = substr($str,$ii,1);
            if($ii%$size==0){
                if($ii>0){
                    $new_str = $new_str.$char.$separation_char;
                } else {
                    $new_str = $new_str.$char;
                }
            } else {
                $new_str = $new_str.$char;
            }
        }
        return $new_str;
    }
    // a private function to separate to turn back to the normal value form 0000-0000-0000-0000 to 00000000000
    private function deseparation($str,$separation_char = "-"){
        $new_str = "";
        for($ii=0;$ii<strlen($str);$ii++){
            $char = substr($str,$ii,1);
            if($char==$separation_char){
                continue;
            } else {
                $new_str = $new_str.$char;
            }
        }
        return $new_str;
    }
    public function hex_to_num($str,$key,$separation_char = "0"){
        $ency = "";
        if($separation_char=="0"){
           $ency =  $str;
        } else {
            $ency = $this->deseparation($str,$separation_char);
        }
        
        $ency = hex2bin($ency);
        $rets = array();
        $singleVal = "";
        $index_cursor = 0;
        //return each;
        for($ii=0;$ii<strlen($ency);$ii++){
            $subCars = substr($ency,$ii,1);
            if($subCars==$key){
                if($ii>0){
                    array_push($rets,$this->to_numbs($singleVal));
                    $singleVal = "";
                    $index_cursor = $ii+strlen($key);
                } else {
                    continue;
                }
            } else if($ii==(strlen($ency)-1)){
               $singleVal = substr($ency,$index_cursor,$ii);
               array_push($rets,$this->to_numbs($singleVal));
                
            } else {
               $singleVal = $singleVal.$subCars;
            }
        }
        
        
        return $rets;
        
    }
    // the function that will return an array of decripted 
    public function finalstrB($finalStr){
            $enc = hex2bin($finalStr);
            $enc = substr($enc,4);
            $productId = '';
            $counts = 0;
            for ($i=0; $i < strlen($enc) ; $i++) { 
                if(ctype_upper(substr($enc,$i,1))){
                   $productId.= substr($enc,$i,1);
                   $counts = $i;
                }
                else {
                    $enc = substr($enc,$counts+1);
                    break;  
                }
            }
            $enc = substr($enc,4);
            $categorieId= '';
            for ($i=0; $i < strlen($enc) ; $i++) { 
                if(ctype_upper(substr($enc,$i,1))){
                   $categorieId.= substr($enc,$i,1);
                   $counts = $i;
                }
                else {
                    $enc = substr($enc,$counts+1);
                    break;

                }
            }
            $enc = substr($enc,4);
            $subcategorieId= '';
            for ($i=0; $i < strlen($enc) ; $i++) { 
                if(ctype_upper(substr($enc,$i,1))){
                   $subcategorieId.= substr($enc,$i,1);
                   $counts = $i;
                }
                else {
                    $enc = substr($enc,$counts+1);
                    break;

                }
            }
            echo $this->tonumbs($productId)."<br>";
            return array($this->tonumbs($productId),$this->tonumbs($categorieId),$this->tonumbs($subcategorieId));
        }
    // a public function to cypher using dictionay
    public function dict($str){
        $zeros = "";
        for($ii=0; $ii<strlen($str);$ii++){
            $position = $this->numPos($ii);
//            echo "Position dict:".$position."<br>";
//            echo "Position rets:".$this->alphaPos(substr($str,$ii,1))."<br>";
            $new_pointer = $position + $this->alphaPos(substr($str,$ii,1));
            //$zeros = $zeros.$new_pointer;
            $zeros = $zeros.$this->alpha_dictionary[($new_pointer%26)];
        }
        return $zeros;
    }
    // a public function to decypher using dictionay
    public function Dedict($str){
        $zeros = "";
        for($ii=0; $ii<strlen($str);$ii++){
            $position = $this->numVal($this->numPos($ii));
//            echo "Position dict:".$position."<br>";
//            echo "Position rets:".$this->alphaPosD(substr($str,$ii,1))."<br>";
            $new_pointer = $this->alphaPosD(substr($str,$ii,1)) - $position;
            if($new_pointer<0)
                $new_pointer = $new_pointer * -1;
            //$zeros = $zeros.$new_pointer;
            $zeros = $zeros.$this->alpha_order[($new_pointer%26)];
        }
        return $zeros;
    }
    // a private function to return an equivalent number in the number hash table
    private function numVal($num){
        if($this->numPos($num)<0){
            return -1;
        } else return $this->num_dictionary[$this->numPos($num)];
    }
    // a function to return a position of a given number
    private function numPos($num){
        $rets = -1;
        for($ii=0; $ii<sizeof($this->num_dictionary);$ii++){
            if(intval($num)==intval($this->num_dictionary[$ii])){
                $rets = $ii;
                break;
            }
        }
        return $rets;
    }
    
    private function alphaPos($c){
        $rets = -1;
        for($ii=0;$ii<sizeof($this->alpha_order);$ii++){
            if(strcmp($c,$this->alpha_order[$ii])==0){
                $rets = $ii;
                break;
            }
        }
        return $rets;
    }
    
    private function alphaPosD($c){
        $rets = -1;
        for($ii=0;$ii<sizeof($this->alpha_dictionary);$ii++){
            if(strcmp($c,$this->alpha_dictionary[$ii])==0){
                $rets = $ii;
                break;
            }
        }
        return $rets;
    }
}
// this is a class to send all informations of selected data in form of JSON format 
// this class has 3 main public variable where $datas is all records from database, structure is an array that will define structure of the fields of data to display and list of defined structure to be displayed
/*
    ########## here there is the explanations of all structure value to be declared for
    example of a 1 element data:
    "name" =>[
                "type" => "VARCHAR",
                "size" => "50",
                "classes" => "table_name w3-text-red",
                "title" => "name",
                "restricted_chars" => ".\/;'",
                "recomended_chars" => "!@#$%^",
                "data_display" => "display_name",
                "data_html" => "false",
                "IDENTICAL" =>".#css selector",
                "NONIDENTICAL" =>".#css selector",
                "MUST_GREATER" =>".#css selector",
                "MUST_LESS" =>".#css selector",
                "MUST_EQUAL" =>".#css selector",
                "MUST_LESS_EQUAL" =>".#css selector",
                "MUST_GRETER_EQUAK" => ".#css selector"
                "MAX" => 9000,
                "MIN" => -9000,
                "EXEPTION"=>[
                    [
                        "val"=>15,
                        "attr"=>"value",
                        "type"="class"
                    ],
                    [
                        "val"=>"name",
                        "attr"=>"value",
                        "type"=>"class"
                    ]
                ]
            ]
    the "name" is the name of the field this must be equivalent to that of MySql column defined in the table, and this is the main root of the structure

       "type" : is the data type to be used in the HTML5 input  form, element, javascript functions and MySql data types available datatype are <the default value is VARCHAR>
       */
       {
       /*
            #CHAR : a fixed length(from 0 to 255, default is 1) string this is always right-padded with space while stored in Mysql           database
            #VARCHAR : a variable length string (from 0 to 65,535) the effective max length is subjected to the maximum row size
            #TEXT : a text with maximum number of characters of 65,535 ((2 to power 16)-1)), this is stored with two byte prefixe to           specify the number of characters in form of byte
            #TINYTEXT : an 8 bit version of TEXT ((2 to power 8)-1) 
            #MEDIUMTEXT : a 24 bit version of TEXT ((2 to power 24)-1)
            #LONGTEXT : a 64 bit version of TEXT ((2 to power 64)-1)
            #BINARY : the same as CHAR but is for  binary characters
            #VARBINARY : the same as VARCHAR but is binary character
            #BLOB : an array of bytes of the maximum size of ((2 to power 16)-1))
            #TINYBLOB : an 8 bit version of BLOB ((2 to power 8)-1)  
            #MEDIUMBLOB : a 24 bit version of BLOB ((2 to power 24)-1)
            #LONGBLOB : a 64 bit version of BLOB ((2 to power 64)-1)
            #ENUM : this is especialy in mysql database datatype where it is a string object whose value is chosen from permitted             values defined at time of column definition
            #SET : a single value chosen from a set up to 64 members (some how the same as ENUM)
            
            #INT: a 4-byte integer when signed ( from -2,147,483,648 to 2,147,483,648) when unsigned (from 0 to 4,294,467,295)
            #TINYINT : a 1-byte version of INT
            #SMALLINT : a 2-byte version of INT
            #MEDIUMINT : a 3-byte version of INT
            #BIGINT : an 8-byte version of INT
            
            #DECIMAL : a fixed point number (M,D) the maximum number of digit of M is 65 and the default is 10 where the maximum                number of digit of D is 30 the default is 0
            #FLOAT : small floating point number allowed numbers are from -3.402823466e38 to -1.175494351e-38 ,0 and 1.175494351e-38          to 3.402823466e38
            #DOUBLE : A double-precision floating-point number, allowable values are -1.7976931348623157E+308 to 
                      -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308
            #REAL : Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is a synonym for FLOAT)
            #BIT : A bit-field type (M), storing M of bits per value (default is 1, maximum is 64)
            #BOOLEAN : A synonym for TINYINT(1), a value of zero is considered false, nonzero values are considered true
            #SERIAL : An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE
            
            #DATE : A date, supported range is 1000-01-01 to 9999-12-31
            #DATETIME : A date and time combination, supported range is 1000-01-01 00:00:00 to 9999-12-31 23:59:59
            #TIMESTAMP : A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 03:14:07 UTC, stored as the number of seconds                since the epoch (1970-01-01 00:00:00 UTC)
            #TIME : A time, range is -838:59:59 to 838:59:59
            #YEAR : A year in four-digit (4, default) or two-digit (2) format, the allowable values are 70 (1970) to 69 (2069) or
                    1901 to 2155 and 0000
                    
            #JSON : Stores and enables efficient access to data in JSON (JavaScript Object Notation) documents
            
            #GEOMERTY : 
            #POINT : 
            #LINE :
            #POLYGON :
            #MULTIPOINT :
            #MULTILINESRTING :
            #MULTIPOLYGON :
            #GEOMETRYCOLLECTION :    
*/       
          }
       /*
       "size" : this is an integer value that will represent the length of the data type for MySql database and HTML5 input
       <the default value is 50>
       "classes" : List of additional css(JS&DOM) classes for the element in HTML Documnet
       <the default value is "">
       "title" : the name of the field for MySql table column and HTML input names
       <the default value is the same as the root name>
       "restricted_chars" : a string containing resticted characters for HTML and Js input validation and PHP validation before                            loading and inserting data in MySql database
       <the default value is "">
       "recomended_chars" : a string containing recomanded chars for HTML input(text and text area)
       <the default value is "">
       "data_display" : a value of data-display attribute in HTML element where this field will be displayed
       <the default value is "">
       "data_html" : a boolean value that decide if the this element should be displayed
       <the default value is true>
       "IDENTICAL" : a css selector for all must identical fields class for html input especialy in password
       <the default value is "">
       "MUST_GREATER" : a css selector for all must greater fields for all for html input especialy numbers
       <the default value is "">
       "MUST_LESS" : a css selector for all lesser fields for all for html input especialy numbers
       <the default value is "">
       "MUST_EQUAL" : a css selector for all equal fields for all for html input especialy numbers
       <the default value is "">
       "MUST_LESS_EQUAL" : a css selector for all less or equal fields for all for html input especialy numbers
       <the default value is "">
       "MUST_LESS_EQUAL" : a css selector for all less or equal fields for all for html input especialy numbers
       <the default value is "">
       "MUST_GRETER_EQUAL" : a css selector for all less or equal fields for all for html input especialy numbers
       <the default value is "">
       "MIN" : a number that specify the maximum number of the field
       <the default value is "">
       "MAX" : a maximum number of the field
       <the default value is "">
       "EXEPTION" : the exeption of the field that are special according to the value that they hold, to add for example a css class to it for JS_STRUCTURE if the value of the of val is # the contents of MySql field will be filled in the HTML attribute value.
       <the default value is ["val"=>"*","attr"=>"*","type"=>"*"] >
       and if the value of the val is * and the value of type is id all elements will be filled with ids according the universal id value
       "EXEPTION_": the same as the above in functioning but the difference is that we will chech the value from another fields instead of the same fields
       
       "_NULL_": is a boolean value to be specified if a mysql field can be null or not
       "DEFAULT": is a value to be specified for the default value of the field.
       
       */
// a class that will save all those identification as an object
class JS_STRUCTURE {
    public $name;
    protected $type; 
    protected $size;
    protected $classes;
    protected $restricted_chars;
    protected $recomended_chars;
    protected $data_display;
    protected $data_html;
    protected $IDENTICAL;
    protected $NONIDENTICAL;
    protected $MUST_GREATER;
    protected $MUST_LESS;
    protected $MUST_EQUAL;
    protected $MUST_LESS_EQUAL;
    protected $MUST_GRETER_EQUAL;
    protected $MAX;
    protected $MIN;
    protected $EXEPTION = [
        ["val"=>"*","attr"=>"*","type"=>"*"]
    ];
    
    protected $EXEPTION_ = [
        ["val"=>"*","attr"=>"*","field"=>"*","type"=>"*"]
    ];
    
    protected $_NULL_ = false;
    
    protected $DEFAULT = "";
    
    protected $ON = [[
        "name"=>"",
        "value"=>""
    ]];
    
    
    
    public const POSSIBLE_DT = [
        "numeric"=>[
            [
                "name"=>"TINYINT",
                "exp"=>"",
                "comment"=>"a 1-byte version of INT"
            ],
            [
                "name"=>"SMALLINT",
                "exp"=>"",
                "comment"=>"a 2-byte version of INT"
            ],
            [
                "name"=>"MEDIUMINT",
                "exp"=>"",
                "comment"=>"a 3-byte version of INT"
            ],
            [
                "name"=>"INT",
                "exp"=>"",
                "comment"=>"a 4-byte integer when signed ( from -2,147,483,648 to 2,147,483,648) when unsigned (from 0 to 4,294,467,295)"
            ],
            [
                "name"=>"BIGINT",
                "exp"=>"",
                "comment"=>"an 8-byte version of INT"
            ],
            [
                "name"=>"DECIMAL",
                "exp"=>"",
                "comment"=>" a fixed point number (M,D) the maximum number of digit of M is 65 and the default is 10 where the maximum                number of digit of D is 30 the default is 0"
            ],
            [
                "name"=>"FLOAT",
                "exp"=>"",
                "comment"=>"small floating point number allowed numbers are from -3.402823466e38 to -1.175494351e-38 ,0 and 1.175494351e-38          to 3.402823466e38"
            ],
            [
                "name"=>"DOUBLE",
                "exp"=>"",
                "comment"=>"A double-precision floating-point number, allowable values are -1.7976931348623157E+308 to 
                      -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308"
            ],
            [
                "name"=>"REAL",
                "exp"=>"",
                "comment"=>"Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is a synonym for FLOAT)"
            ],
            [
                "name"=>"BIT",
                "exp"=>"",
                "comment"=>"A bit-field type (M), storing M of bits per value (default is 1, maximum is 64)"
            ],
            [
                "name"=>"BOOLEAN",
                "exp"=>"",
                "comment"=>"A synonym for TINYINT(1), a value of zero is considered false, nonzero values are considered true"
            ],
            [
                "name"=>"SERIAL",
                "exp"=>"",
                "comment"=>"An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE"
            ],
        ],
        "date_and_time"=>[
            [
                "name"=>"DATE",
                "exp"=>"",
                "comment"=>"A date, supported range is 1000-01-01 to 9999-12-31"
            ],
            [
                "name"=>"DATETIME",
                "exp"=>"",
                "comment"=>"A date and time combination, supported range is 1000-01-01 00:00:00 to 9999-12-31 23:59:59"
            ],
            [
                "name"=>"TIMESTAMP",
                "exp"=>"",
                "comment"=>"A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 03:14:07 UTC, stored as the number of seconds                since the epoch (1970-01-01 00:00:00 UTC)"
            ],
            [
                "name"=>"TIME",
                "exp"=>"",
                "comment"=>"A time, range is -838:59:59 to 838:59:59"
            ],
            [
                "name"=>"YEAR",
                "exp"=>"",
                "comment"=>"A year in four-digit (4, default) or two-digit (2) format, the allowable values are 70 (1970) to 69 (2069) or
                    1901 to 2155 and 0000"
            ],
        ],
        "string"=>[
            [
                "name"=>"CHAR",
                "exp"=>"",
                "comment"=>"a fixed length(from 0 to 255, default is 1) string this is always right-padded with space while stored in Mysql           database"
            ],
            [
                "name"=>"VARCHAR",
                "exp"=>"",
                "comment"=>"a variable length string (from 0 to 65,535) the effective max length is subjected to the maximum row size"
            ],
            [
                "name"=>"TEXT",
                "exp"=>"",
                "comment"=>"a text with maximum number of characters of 65,535 ((2 to power 16)-1)), this is stored with two byte prefixe to           specify the number of characters in form of byte"
            ],
            [
                "name"=>"TINYTEXT",
                "exp"=>"",
                "comment"=>"an 8 bit version of TEXT ((2 to power 8)-1)"
            ],
            [
                "name"=>"MEDIUMTEXT",
                "exp"=>"",
                "comment"=>"a 24 bit version of TEXT ((2 to power 24)-1)"
            ],
            [
                "name"=>"LONGTEXT",
                "exp"=>"",
                "comment"=>"a 64 bit version of TEXT ((2 to power 64)-1)"
            ],
            [
                "name"=>"BINARY",
                "exp"=>"",
                "comment"=>"the same as CHAR but is for  binary characters"
            ],
            [
                "name"=>"VARBINARY",
                "exp"=>"",
                "comment"=>"the same as VARCHAR but is binary character"
            ],
            [
                "name"=>"BLOB",
                "exp"=>"",
                "comment"=>"an array of bytes of the maximum size of ((2 to power 16)-1))"
            ],
            [
                "name"=>"TINYBLOB",
                "exp"=>"",
                "comment"=>"an 8 bit version of BLOB ((2 to power 8)-1)"
            ],
            [
                "name"=>"MEDIUMBLOB",
                "exp"=>"",
                "comment"=>"a 24 bit version of BLOB ((2 to power 24)-1)"
            ],
            [
                "name"=>"LONGBLOB",
                "exp"=>"",
                "comment"=>"a 64 bit version of BLOB ((2 to power 64)-1)"
            ],
            [
                "name"=>"ENUM",
                "exp"=>"",
                "comment"=>"this is especialy in mysql database datatype where it is a string object whose value is chosen from permitted             values defined at time of column definition"
            ],
            [
                "name"=>"SET",
                "exp"=>"",
                "comment"=>"a single value chosen from a set up to 64 members (some how the same as ENUM)"
            ],
        ],
        "JSON"=>[
            [
                "name"=>"JSON",
                "exp"=>"",
                "comment"=>"Stores and enables efficient access to data in JSON (JavaScript Object Notation) documents"
            ]
        ],
        "Spatial"=>[
            [
                "name"=>"GEOMETRY",
                "exp"=>"",
                "comment"=>"A type that can store a geometry of any type"
            ],
            [
                "name"=>"POINT",
                "exp"=>"",
                "comment"=>"A point in 2-dimensional space"
            ],
            [
                "name"=>"LINESTRING",
                "exp"=>"",
                "comment"=>"A curve with linear interpolation between points"
            ],
            [
                "name"=>"POLYGON",
                "exp"=>"",
                "comment"=>"A polygon"
            ],
            [
                "name"=>"MULTIPOINT",
                "exp"=>"",
                "comment"=>"A collection of points"
            ],
            [
                "name"=>"MULTILINESTRING",
                "exp"=>"",
                "comment"=>"A collection of curves with linear interpolation between points"
            ],
            [
                "name"=>"GEOMETRYCOLLECTION",
                "exp"=>"",
                "comment"=>"A collection of geometry objects of any type"
            ],
            [
                "name"=>"MULTIPOLYGON",
                "exp"=>"",
                "comment"=>"A collection of polygons"
            ]
        ]
    ];
    
    function __construct($NAME){
        $this->name = $NAME;
        $this->type = "VARCHAR";
        $this->size = 50;
        $this->classes = "";
        $this->restricted_chars = "";
        $this->recomended_chars = "";
        $this->data_display = "";
        $this->data_html = true;
        $this->IDENTICAL = "";
        $this->NONIDENTICAL = "";
        $this->MUST_GREATER = "";
        $this->MUST_LESS = "";
        $this->MUST_EQUAL = "";
        $this->MUST_LESS_EQUAL = "";
        $this->MUST_GRETER_EQUAL = "";
    }
    public function Stype($VAL){
        $this->type = $VAL;
        return $this;
    }
    public function Ssize($VAL){
        $this->size = $VAL;
        return $this;
    }
    public function Sclasses($VAL){
        if($this->classes==""){
            $this->classes .= $VAL;
        } else $this->classes .= " ".$VAL;
       return $this;
    }
    public function Srestrict($VAL){
        $this->restricted_chars .= $VAL;
        return $this;
    }
    public function Srecomend($VAL){
        $this->recomended_chars .= $VAL;
        return $this;
    }
    public function Sdisplay($VAL){
        $this->data_display = $VAL;
        return $this;
    }
    public function Shtml($VAL){
        $this->data_html = $VAL;
        return $this;
    }
    public function SIDENTICAL($VAL){
        if($this->IDENTICAL==""){
            $this->IDENTICAL .= " ".$VAL;
        } else $this->IDENTICAL .= " , ".$VAL;
        return $this;
    }
    public function SNONIDENTICAL($VAL){
        if($this->NONIDENTICAL==""){
            $this->NONIDENTICAL .= $VAL;
        } else {
            $this->NONIDENTICAL .= " , ".$VAL;
        }
        
        return $this;
    }
    public function GREATER($VAL){
        if($this->MUST_GREATER==""){
            $this->MUST_GREATER .= $VAL;
        } else {
            $this->MUST_GREATER .= " , ".$VAL;
        }
        
        return $this;
    }
    public function LESS($VAL){
        if($this->MUST_LESS==""){
           $this->MUST_LESS .= $VAL;
        } else {
           $this->MUST_LESS .= " , ".$VAL; 
        }
        
        return $this;
    }
    public function EQUAL($VAL){
        if($this->MUST_EQUAL==""){
            $this->MUST_EQUAL .= $VAL;
        } else {
            $this->MUST_EQUAL .= " , ".$VAL;
        }   
        return $this;
    }
    public function LESS_EQUAL($VAL){
        if($this->MUST_LESS_EQUAL==""){
            $this->MUST_LESS_EQUAL .= $VAL;
        } else{
            $this->MUST_LESS_EQUAL .= " , ".$VAL;
        }   
        return $this;
    }
    public function GRETER_EQUAL($VAL){
        if($this->MUST_GRETER_EQUAL==""){
            $this->MUST_GRETER_EQUAL .= $VAL;
        } else {
            $this->MUST_GRETER_EQUAL .= " , ".$VAL;
        }
        
        return $this;
    }
    public function SMAX($VAL){
        $this->MAX = $VAL;
        return $this;
    }
    public function SMIN($VAL){
        $this->MIN = $VAL;
        return $this;
    }
    public function struct(){
        $SRTUCT = [
                "type" => $this->type,
                "size" => $this->size,
                "classes" => $this->classes,
                "title" => $this->name,
                "restricted_chars" => $this->restricted_chars,
                "recomended_chars" => $this->recomended_chars,
                "data_display" => $this->data_display,
                "data_html" => $this->data_html,
                "IDENTICAL" => $this->IDENTICAL,
                "NONIDENTICAL" => $this->NONIDENTICAL,
                "MUST_GREATER" => $this->MUST_GREATER,
                "MUST_LESS" => $this->MUST_LESS,
                "MUST_EQUAL" => $this->MUST_EQUAL,
                "MUST_LESS_EQUAL" => $this->MUST_LESS_EQUAL,
                "MUST_GRETER_EQUAL" => $this->MUST_GRETER_EQUAL,
                "MAX" => $this->MAX,
                "MIN" => $this->MIN,
                "EXEPTION"=>$this->EXEPTION,
                "EXEPTION_"=>$this->EXEPTION_
            ];
       return $SRTUCT;
    }
    public function exept($arr){
        array_push($this->EXEPTION,$arr);
        return $this;
    }
    public function exept_($arr){
        array_push($this->EXEPTION_,$arr);
        return $this;
    }
    public function _null_($val = true){
        $this->_NULL_ = $val;
        return $this;
    }
    public function _default_($val){
        $this->DEFAULT = $val;
    }
    public function on($name,$value){
        if($this->ON[0]["name"]==""){
            $this->ON[0]["name"] = $name;
            $this->ON[0]["value"] = $value;
        } else {
            $newOn = [
              "name"=>$name,
              "value"=>$value
            ];
            array_push($this->ON,$newOn);
        }
    }
    // a function to return the html table row of the structure
    public function tr(){
        $rets = '<tr>';
        $rets = $rets."<td>".$this->name."</td>";
        $rets = $rets."<td>".$this->type."</td>";
        $rets = $rets."<td>".$this->size."</td>";
        $rets = $rets."<td>".$this->classes."</td>";
        $rets = $rets."<td>".$this->restricted_chars."</td>";
        $rets = $rets."<td>".$this->size."</td>";
        $rets = $rets."</tr>";
        return $rets;
    }
    
    
}
// a function to create a to return a JS_STRUCTURE object
function JS_ST($name){
    return new JS_STRUCTURE($name);
}
class JS_API {
    private $stucture_ex = [
            "name" =>[
                "type" => "VARCHAR",
                "size" => 50,
                "classes" => "table_name w3-text-red",
                "title" => "name",
                "restricted_chars" => ".\/;'",
                "recomended_chars" => "!@#$%^",
                "data_display" => "display_name",
                "data_html" => true,
                "IDENTICAL" =>".#css selector",
                "NONIDENTICAL" =>".#css selector",
                "MUST_GREATER" =>".#css selector",
                "MUST_LESS" =>".#css selector",
                "MUST_EQUAL" =>".#css selector",
                "MUST_LESS_EQUAL" =>".#css selector",
                "MUST_GRETER_EQUAL" => ".#css selector",
                "MAX" => 9000,
                "MIN" => -9000,
                "EXEPTION"=>[
                    [
                        "val"=>15,
                        "attr"=>"value",
                        "type"=>"class"
                    ],
                    [
                        "val"=>"name",
                        "attr"=>"value",
                        "type"=>"class"
                    ]
                ]
            ],
            "registed_day" =>[
                "type" => "date",
                "size" => "_",
                "classes" => "w3-green date pip-date",
                "title" => "registed_day",
                "restricted_chars" => "./;'",
                "recomended_chars" => "!@#$%^",
                "data_display" => "display_date",
                "data_html" => true,
                "IDENTICAL" =>".#css selector",
                "NONIDENTICAL" =>".#css selector",
                "MUST_GREATER" =>".#css selector",
                "MUST_LESS" =>".#css selector",
                "MUST_EQUAL" =>".#css selector",
                "MUST_LESS_EQUAL" =>".#css selector",
                "MUST_GRETER_EQUAK" => ".#css selector",
                "MAX" => 9000,
                "MIN" => -9000,
                "EXEPTION"=>[
                    [
                        "val"=>15,
                        "attr"=>"value",
                        "type"=>"class"
                    ],
                    [
                        "val"=>"name",
                        "attr"=>"value",
                        "type"=>"class"
                    ]
                ]
            ]
    ];
    private $exptions = [
        [
           "val"=>"*",
           "field"=>"*",
           "attr"=>"*",
           "type"=>"*"
        ]
    ];
    private $more_extra = [];
    function __construct($PIP_Array,$JS_STRUCTURE = []){
        $this->datas = $PIP_Array;
        
        for($ii=0;$ii<$this->datas->size;$ii++){
            if(!isset($this->datas->AV[$ii][0])){
              echo "(".$ii.")";$this->datas->List($ii);  
            }

                 
            
            $this->datas = $this
                ->datas
                ->push($ii,"universal_id",$this->datas->AV[$ii][0]);
            
        }
        $this->structure = $JS_STRUCTURE;
        $stucture_ex1 = new JS_STRUCTURE("name");
        $stucture_ex2 = new JS_STRUCTURE("registed_day");
        
        $stucture_ex1
            ->Ssize(50)
            ->Sclasses("table_name")
            ->Sclasses("w3-text-red")
            ->Srestrict(";")
            ->Srestrict("%")
            ->Srecomend("#$")
            ->Srecomend("*@")
            ->Sdisplay("display_name_text")
            ->Sdisplay("display_name")
            ->Shtml(false)
            ->Shtml(true)
            ->SIDENTICAL("#surnames .wellcome")
            ->SIDENTICAL("#otherName .wellcome_you")
            ->GREATER(".prices")
            ->GREATER(".promotion")
            ->GREATER(".promotion")
            ->LESS(".age")
            ->LESS("#wellcome_home .style")
            ->EQUAL("#POINT .space, #HORIZONTAL .crosses")
            ->LESS_EQUAL("#POOL .Home")
            ->LESS_EQUAL("#cnn .and_bbc")
            ->GRETER_EQUAL(".holograme")
            ->Sclasses("w3-border-bottom");
        
        $stucture_ex2
            ->Ssize(50)
            ->Sclasses("date pip-date")
            ->Sclasses("w3-green")
            ->Srestrict(";")
            ->Srestrict("%")
            ->Srecomend("#$")
            ->Srecomend("*@")
            ->Sdisplay("display_date_text")
            ->Sdisplay("display_date")
            ->Shtml(false)
            ->Shtml(true)
            ->SIDENTICAL("#surnames .wellcome")
            ->SIDENTICAL("#otherName .wellcome_you")
            ->GREATER(".prices")
            ->GREATER(".promotion")
            ->GREATER(".promotion")
            ->LESS(".age")
            ->LESS("#wellcome_home .style")
            ->EQUAL("#POINT .space, #HORIZONTAL .crosses")
            ->LESS_EQUAL("#POOL .Home")
            ->LESS_EQUAL("#cnn .and_bbc")
            ->GRETER_EQUAL(".holograme")
            ->Sclasses("w3-border-bottom")
            ->SMAX(9000)
            ->SMIN(-9000)
            ->Stype("TIMESTAMP");
        
        $this->stucture_ex[$stucture_ex1->name] = $stucture_ex1->struct();
        $this->stucture_ex[$stucture_ex2->name] = $stucture_ex2->struct();
        
    }
    public function struct($JS_STRUCTURES){
        $this->JS_STRUCTURE[$JS_STRUCTURES->name] = $JS_STRUCTURES->struct();
        return $this;
    }
    // a public function that will send all default information on this data
    public function send($parameter){
        $TYPE = "data";
        if(isset($_POST[$parameter])){
           $TYPE =  $_POST[$parameter];
        } else if(isset($_GET[$parameter])){
           $TYPE =  $_GET[$parameter];
        }
        
        switch($TYPE){
            case "data":{
                   echo $this->datas->JV; 
                   break;
                }
            case "struct":{
                    echo json_encode($this->stucture_ex);
                    break;
                }
            case "fields":{
                    $OUT_PUT = array();
                    for($cc=0;$cc<sizeof($this->stucture_ex);$cc++){
                        $OUT_PUT_c = ["index"=>$cc,"name"=>array_keys($this->stucture_ex)[$cc]];
                        array_push($OUT_PUT,$OUT_PUT_c);
                    }
                    print_r(json_encode($OUT_PUT));
                    break;
            }
            
            default:{
                   echo $this->datas->JV; 
                   break;
            }
        }
    }
    public function emit($parameter){
        $TYPE = "all";
        if(isset($_POST[$parameter])){
           $TYPE =  $_POST[$parameter];
        }
        else if(isset($_GET[$parameter])){
           $TYPE =  $_GET[$parameter];
        }
        
        switch($TYPE){
            case "data":{
                   echo $this->datas->JV; 
                   break;
                }
            case "struct":{
                    echo json_encode($this->JS_STRUCTURE);
                    break;
                }
            case "fields":{
                    $OUT_PUT = array();
                    for($cc=0;$cc<sizeof($this->JS_STRUCTURE);$cc++){
                        $OUT_PUT_c = ["index"=>$cc,"name"=>array_keys($this->JS_STRUCTURE)[$cc]];
                        array_push($OUT_PUT,$OUT_PUT_c);
                    }
                    print_r(json_encode($OUT_PUT));
                    break;
            }
            case "exeption":{
                echo json_encode($this->exptions);
                break;
            }
            case "extra":{
                echo json_encode($this->more_extra);
                break;
            }
            case "all":{
                $outs = '{"data":'.$this->datas->JV.",";
                $outs .= '"struct":'.json_encode($this->JS_STRUCTURE).",";
                $outs .= '"exeption":'.json_encode($this->exptions).",";
                $OUT_PUT = array();
                    for($cc=0;$cc<sizeof($this->JS_STRUCTURE);$cc++){
                        $OUT_PUT_c = ["index"=>$cc,"name"=>array_keys($this->JS_STRUCTURE)[$cc]];
                        array_push($OUT_PUT,$OUT_PUT_c);
                }
                $outs .='"extra":'.json_encode($this->more_extra).",";
                $outs .='"fields":'.json_encode($OUT_PUT)."}";
                print_r($outs);
                break;
            }
            case "data_nd_extra":{
                $outs = '{"data":'.$this->datas->JV.',';
                $outs .= '"extra":'.json_encode($this->more_extra).'}';
                print_r($outs);
                break;
            }
            default:{
                   echo $this->datas->JV; 
                   break;
            }
        }
    }
    // a function that will put some exptions like data_html and classes and many more.
    /*
        $arr = [
           $val="value to be checked",
           $field="field name to be checking in",
           $attr="value of the attribute",
           $type="type of the attribute"
        ]
    */
    public function exept($arr){
        array_push($this->exptions,$arr);
        return $this;
    }
    // a function to triger the receive event
    public function receive(){
        
    }
    // a function to bind an event to the element
    public function on($event = ""){
        
    }
    // a function to add extra data that are not in the loop
    public function extra($ext){
        array_push($this->more_extra,$ext);
        return $this;
    }
}
// a class to deal with http request data in the header
class _HTTP{
    private $type;
    private $is_set;
    private $header;
    private $value;
    function __construct($header = ""){
        $this->header = $header;
        if($header==""){
            $this->type = 100;
            $this->is_set = true;
            $this->value = "none";
        } else if(isset($_POST[$header])){
            $this->type = 0;
            $this->is_set = true;
            $this->value = $_POST[$header];
        } else if(isset($_GET[$header])){
            $this->type = 1;
            $this->is_set = true;
            $this->value = $_GET[$header];
        } else {
            $this->type = -1;
            $this->is_set = false;
            $this->value = "-0-0-0-0-0-0";   
        }
    }
    
    public function set(){
        return $this->is_set;
    }
    
    public function val($val = ""){
        if(pipStr($val)->length()){
            $this->value = $val;
            return $this;
        }
        return $this->value;
    }
    
    public function name(){
        return $this->header;
    }
    
}
// a function to return an _HTTP object instance
function http($header){
    return new _HTTP($header);
}
// a function to create an array from a given bracket data
function bracketDatas($datas){
    $checked = [];
    $single = "";
    for($i=0;$i<strlen($datas);$i++){
        $subs = substr($datas,$i,1);
        if($subs=="("){
            $single = ""; 
        } else if($subs==")"){
            array_push($checked,$single);
        } else {
            $single = $single.$subs;
        }
    }
    return $checked;
}
// a function to create a bracket data in form of (data)(data) from a given array
function dataBrackets($arr){
    $rets = "";
    
    for($i=0; $i<sizeof($arr);$i++){
        $rets = $rets."(".$arr[$i].")";
    }
    return $rets;
}
// a function to add a new record in the bracket data
function addBrackets($bracket,$datas){
    $arr  = bracketDatas($bracket);
    if(contains_arr($arr,$datas)){
        return $datas;
    }
    else{
      if(is_array($datas)){
        for($i=0;$i<sizeof($datas);$i++)
            array_push($arr,$datas[$i]);
        } else {
            array_push($arr,$datas);
        }
        return dataBrackets($arr);  
    }
    
}
// a function to check if the given value is in the given array
function contains_arr($arr,$data){
    $rets = false;
    for($i=0;$i<sizeof($arr);$i++){
        if($arr[$i]==$data){
            $rets = true;
            break;
        }
    }
    return $rets;
}
//a function to remove an index value in the array
function remove_arr($arr,$data){
    if(contains_arr($arr,$data)){
        $new_arr = array();
        for($ii=0;$ii<sizeof($arr);$ii++){
           if(!($arr[$ii]==$data))
              array_push($new_arr,$arr[$ii]); 
        }
        return $new_arr;
    } else return $arr;
}
// a function to return the index of the data inside an array
function index_arr($arr,$data){
    $rets = -1;
    for($ii=0;$ii<sizeof($arr);$ii++){
        if($arr[$ii]==$data){
            $rets = $ii;
            break;
        }
    }
    return $rets;
}
// a function to remove an item inside a bracket data
function delBrackets($bracket,$datas){
    $arr  = bracketDatas($bracket);
    $arr2 = [];
    if(is_array($datas)){
        for($i=0;$i<sizeof($datas);$i++){
            for($ii=0;$ii<sizeof($datas);$ii++){
                if($arr[$i]==$arr[$ii]){
                    continue;
                } else if(contains_arr($arr2,$arr[$i])){
                    continue;
                } else {
                    array_push($arr2,$arr[$i]);
                }
            }
        }
    } else {
        for($i=0;$i<sizeof($arr);$i++){
            if($arr[$i]==$datas){
                continue;
            } else {
                array_push($arr2,$arr[$i]);
            }
        }
    }
    return dataBrackets($arr2);
    
}
// a function to return the index of the given dictionary within a given array
function index_dict($arr, $dict){
    $index = -1;
    $keys = array_keys($dict);
    for($ii = 0; $ii<sizeof($arr);$ii++){
        for($iii=0;$iii<sizeof($keys);$iii++){
            if(!isset($arr[0][$keys[$iii]])){
                break;
            }
        }
    }
}

class PIP_numbers {
    public $num = 0;
    function __construct($number){
        if(is_numeric($number))
            $this->num = $number;
    }
    function addOrdinalNumberSuffix(){
            if (!in_array(($this->num % 100),array(11,12,13))){
                switch ($this->num % 10) {
                    // Handle 1st, 2nd, 3rd
                    case 1:  return $this->num.'st';
                    case 2:  return $this->num.'nd';
                    case 3:  return $this->num.'rd';
                }
            }
            return $this->num.'th';
        }
    function Ordinal(){
        return $this->addOrdinalNumberSuffix();
    }
    function Numerical(){
        $num = $this->num;
    }
    function fibonaci(){
        if($this->num == 0)
            return new PIP_numbers(1);
        else if($this->num == 1)
            return new PIP_numbers(1);
        else{
            $prev = new PIP_numbers($this->num-1);
            $next = new PIP_numbers($this->num-2);
            return new PIP_numbers($prev->num + $next->num);
        } 
    }
    // a function to return an absolute value of a number
    public function absolute(){
        if($this->num>0)
            return $this->num;
        else if($this->num==0){
            return $this->num;
        } else return $this->num*(-1);
    }
}
function PIP_number($numb){
    return new PIP_numbers($numb);
}
function PIP_En($original,$keyNumber){
    return new pipEncrypt($original,$keyNumber);
}

function Listi($EL){
    echo "<pre>".print_r($EL,1)."</pre>";
}
// a class that will used to as a pointer of the PIP_Array to avoid for loops
class PIP_ArrayCursor{
    private $position = 0;
    private $ELEMENTS = null;
    // a private variable to tell if we are on the first postion
    private $f = true;
    // a private to tell us if we are on the last element
    private $l = false;
    function __construct($EL, $pos = 0){
        if($EL instanceof PIP_Array)
           $this->ELEMENTS = $EL;
        else if(is_array($EL))
            $this->ELEMENTS = new PIP_Array($EL);
        else $this->ELEMENTS = pipArr();
        $this->position = $pos;
        
        if($this->ELEMENTS->height()){
            if($this->position==0){
                $this->f = true;
            } else if($this->position==($this->ELEMENTS->height()-1)){
                $this->l = true;
            }     
        }
        
        if($this->f){
            $this->l = false;
        } else {
            $this->l = true;
        }
    }
    // a function to restart th cursor
    public function start(){
        $this->position = 0;
        $this->f = true;
        $this->l = false;
        return $this;
    }
    // the function to point the cursor to the 1st element
    public function first(){
        if($this->ELEMENTS->height()>0){
            $this->position = 0;
            return new ARR_TO_JSON($this->ELEMENTS->AV,$this->position);
        }
        else return NULL;
    }
    // a function to move to the next element if it exist it will return true and false otherwise
    public function next(){
        if($this->ELEMENTS->size==1){
            if(!$this->f){
                return false;
            } else {
                $this->f = false;
                $this->l = true;
                return true;
            }
        }
        else if($this->position<($this->ELEMENTS->size-1)){
            if(!$this->f){
              $this->position++;
              if($this->position==($this->ELEMENTS->size-1)){
                  $this->l = true;
              }
            } else {
              $this->f = false;
            }
            return true;
        }
        else {
           $this->position = $this->ELEMENTS->size-1;
           return false;
        } 
    }
    // a function to move the cursor to the previous element
    public function prev(){
        if($this->position>0){
            if(!$this->l){
                $this->position--;
                if($this->position==0){
                   $this->f = true; 
                }
            } else {
                $this->l = false;
            }
            return true;
        } else {
            $this->position = 0;
            return false;
        }
    }
    // a function to move the cursor to the last element 
    public function last(){
        if($this->ELEMENTS->height()){
            $this->position = $this->ELEMENTS->height()-1;
            return new ARR_TO_JSON($this->ELEMENTS->AV,$this->position);
        }
        else return NULL;
    }
    // a function to return an array to json object of the current position
    public function JS(){
        return new ARR_TO_JSON($this->ELEMENTS->AV,$this->position);
    }
    // a funcction to push a new element in the data of the cursor
    public function push($name,$data){
        return new PIP_ArrayCursor($this->ELEMENTS->push($this->position,$name,$data),$this->position);
    } 
    // a function to reverse the element
    public function reverse(){
        $this->ELEMENTS = $this->ELEMENTS->reverse();
        return $this;
    }
    // a function to access the pipArray element
    public function el(){
        return $this->ELEMENTS;
    }
    // a function to return the height of element
    public function height(){
        return $this->ELEMENTS->height();
    }
    // a function to return the width of the elment
    public function width(){
        return $this->ELEMENTS->width();
    }
    // a function to echo the current position
    public function printi($index = 0){
        $this->ELEMENTS->printi($this->position,$index);
    }
    // a function to return the current positon in Ordinary format
    public function n(){
        return PIP_number(($this->position+1))->addOrdinalNumberSuffix();
    }
    // a function to reurn the current postion 
    public function pos(){
        return $this->position;
    }
    //a function for dislaying all values while debugging
    public function list(){
        $this->ELEMENTS->List();
        $first = "on the first element";
        $lasts = "on the last element";
        if(!$this->f){
            $first = "not on the first element";
        }
        if(!$this->l){
            $lasts = "not on the last element";
        }
        $rets = [
            "position"=>$this->position,
            "first"=>$first,
            "last"=>$lasts
        ];
        Listi($rets);
    }
    // a function for filtering the ELEMENT the same as the PIP_Array _gets_
    public function _gets_($INDEX, $VALUE = "", $TYPE = "REMOVE"){
        return $this->ELEMENTS->_gets_($INDEX,$VALUE,$TYPE)->CR();
    }
    //a function to get the id from the data of the cursor
    public function id(){
        return $this->ELEMENTS->id($this->position);
    }
}
?>
