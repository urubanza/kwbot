CREATE TABLE IF NOT EXISTS `vehicles_logs` (
                              `vehicles_logs_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `cont` varchar(100) NOT NULL,
                                    `type` int(11) NOT NULL,
                                    `added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                    `vehicles_id` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1