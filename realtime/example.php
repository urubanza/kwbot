<?php

   if(isset($_GET["greeting"])){
        echo "welcome home ".$_GET["greeting"];
   } else {
       echo "not greeting";
   }
?>