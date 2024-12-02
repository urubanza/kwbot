CREATE TABLE IF NOT EXISTS `devices_type` (
                              `devices_type_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` varchar(20) NOT NULL,
								    `description` longtext NOT NULL,
								    `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
								    `icon` varchar(100) NOT NULL,
								    `cover` varchar(100) NOT NULL,
								    `robots_type_id` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1