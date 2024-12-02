CREATE TABLE IF NOT EXISTS `robots_type` (
                              `robots_type_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` varchar(30) NOT NULL,
							`descriotion` longtext NOT NULL,
							`icon` varchar(100) NOT NULL,
							`cover` varchar(100) NOT NULL,
							`created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()) ENGINE=MyISAM DEFAULT CHARSET=latin1