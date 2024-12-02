<?php
    $ACCOUNTS = $kwbot->client_list($USER_ACCOUNTS);
    // setting rules to all accounts for loggin 
    for($ii=0;$ii<sizeof($USER_ACCOUNTS);$ii++){
        $ACCOUNTS[$ii]->SET_RULES($user_name_rules,$password_rules);
        if($ACCOUNTS[$ii]->logged_in||$ACCOUNTS[$USER_ACCOUNTS[$ii]]->logged_in){
            $whos_log = $ii;
            //$$USER_ACCOUNTS_NAMES[$ii] = true;   
        }
    }

