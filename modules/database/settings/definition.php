<?php    
    $kwbot = database("kwbot");
    $kwbot->create();


   
    // definition of tables
    $robots_type = $kwbot
                ->table("robots_type")
                ->initials("`name` varchar(30) NOT NULL,
							`descriotion` longtext NOT NULL,
							`icon` varchar(100) NOT NULL,
							`cover` varchar(100) NOT NULL,
							`created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()",
                            "MyISAM");

    $vehicles = $kwbot
                ->table("vehicles")
                ->initials("`name` varchar(20) NOT NULL,
							`serial` varchar(100) NOT NULL,
							`vehicles_type_id` int(11) NOT NULL",
                            "MyISAM");

    $vehicles_type = $kwbot
                        ->table("vehicles_type")
                        ->initials("`name` varchar(20) NOT NULL,
								    `description` longtext NOT NULL,
								    `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
								    `icon` varchar(100) NOT NULL,
								    `cover` varchar(100) NOT NULL,
								    `robots_type_id` int(11) NOT NULL",
                                    "MyISAM");
	$vehicles_logs = $kwbot
                        ->table("vehicles_logs")
                        ->initials("`cont` varchar(100) NOT NULL,
                                    `type` int(11) NOT NULL,
                                    `added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                    `vehicles_id` int(11) NOT NULL","MyISAM");

    $path = $kwbot
                ->table("path")
                ->initials("`path_name` varchar(20) NOT NULL,
                              `coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                              `creation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                              `user_id` int(11) NOT NULL","MyISAM");


    $obstacles = $kwbot
                    ->table("obstacles")
                    ->initials("`distance` FLOAT NOT NULL,
                                `ultra_id` INT NOT NULL,
                                `path_id` INT NOT NULL,
                                `path_loc` INT NOT NULL,
                                `vehicles_id` int(11) NOT NULL","MyISAM");

    $obstacles_map = $kwbot
                    ->table("obstacles_map")
                    ->initials("`distance` FLOAT NOT NULL,
                                `ultra_id` INT NOT NULL,
                                `vehicles_id` int(11) NOT NULL","MyISAM");

    $ultra = $kwbot
                    ->table("ultra")
                    ->initials("`name` varchar(2) NOT NULL","MyISAM");

