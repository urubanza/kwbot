<?php
    $locs = "";
    include($locs."modules/header.php");
    $temp = new template("unkown");
    $auth = new auth("login","loginme");
    
    if(http("logout")->set()){
        if($auth->users($ACCOUNTS)>-1){
            if($ACCOUNTS[$whos_log]->logout())
                template::goto($locs);
        } else {
            $temp
                ->variables("names","Login to access the Dashboard")
                ->footer("login/login.html")
                ->header("header.html")
                ->body("login.html")
                ->variables("localhost","Login first")
                ->variables("JS_CONFIG",config_js($locs,1))
                ->variables("JS_FUNCTIONS",functions_js($locs,1))
                ->render()
                ->echo();
        }    
    }
    else {
        if($auth->users($ACCOUNTS)>-1){
            $names = $ACCOUNTS[$whos_log]->_gets_();
            $fname = "";
            if($names->size){
               $fname = $names->JS()->fname;
               $names = $names->JS()->fname." ".$names->JS()->lname; 
            } else {
                $names = "Unkown";
            }

            $temp
                ->variables("names",$names)
                ->variables("fname",$fname)
                ->variables("JS_CONFIG",config_js($locs,1))
                ->variables("JS_FUNCTIONS",functions_js($locs,1))
                ->abody("","",[template::attr("id","wrapper")])
                //->bbody("","",[template::attr("id","wrapper")],0,5)
                ->var("left_navigations",$temp->load("navigations.html"))
                ->var("top_navigation",$temp->load("top_nav.html"))
                ->var("main_footer",$temp->load("main_footer.html"))
                ->body("_dashboard/main.html")
                ->body("logoutpopup.html")
                ->footer("footer.html")
                ->header("header.html")
                ->render()
                ->echo();
        }
        else if($auth->found){
            if($auth->login($kwbot,[$ACCOUNTS[0]],["password","email"])){
                template::refresh();
            }
            else {
                    $temp
                         ->variables("localhost",$auth->Smessage())
                         ->footer("login/login.html")
                         ->body("login.html")
                         ->header("header.html")
                         ->variables("JS_CONFIG",config_js($locs,1))
                         ->variables("JS_FUNCTIONS",functions_js($locs,1))
                         ->render()
                         ->echo();
            }
        }
        else{
           $temp
                ->variables("names","Login to access the Dashboard")
                ->body("login.html")
                ->footer("login/login.html")
                ->variables("JS_CONFIG",config_js($locs,1))
                ->variables("JS_FUNCTIONS",functions_js($locs,1))
                ->header("header.html")
                ->variables("localhost","Login first")
                ->render()
                ->echo(); 
        }    
    }
?>

    
    