<?php
    // this is the file where the definition of the user that will be needed in the whole platfom
   

   // here are rules that will be used in passsword and username
   $user_name_rules = ["1","2","3","4","5","6","7","8","9","0","'"];
   $password_rules = ["!","@","#","$","%","^","&","*","1","2","3","4","5","6","7","8","9","0"];

   // a list of available users in the system

   $USER_ACCOUNTS_NAMES = [
       "administration",
       "users"
   ];
   
   $USER_ACCOUNTS = [
       "administration",
       "users"
   ];
   // a variable to save the current logged in user
   $whos_log = -1;
  
   // booleans to keeps type of logged in users 
    $administration = false;
    $farmer = false;