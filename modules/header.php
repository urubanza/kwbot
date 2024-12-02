<?php
    // inclusion of core functions and procedural functions
    include($locs."functions/php/functions.php");
    include($locs."functions/php/template.php");
    include($locs."functions/SOP/php/conne.php");
    include($locs."functions/php/fileUpload.php");
    // the root database creation and table definition
    include($locs."modules/database/settings/definition.php");
    //creation of defined tables 
    include($locs."modules/database/settings/creation.php");
    //tables relationships definition
    include($locs."modules/database/settings/relationship.php");

    // user accounts definitions 
    include($locs."modules/database/users/list.php");
    include($locs."modules/database/users/accounts.php");
?>