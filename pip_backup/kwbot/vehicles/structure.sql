CREATE TABLE IF NOT EXISTS `vehicles` (
                              `vehicles_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` varchar(20) NOT NULL,
							`serial` varchar(100) NOT NULL,
							`vehicles_type_id` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1