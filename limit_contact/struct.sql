CREATE TABLE `oc_t_limit_contact` (
  `s_ip` varchar(16) NOT NULL,
  `dt_date_time` datetime NOT NULL,
  `s_email_from` varchar(45) DEFAULT NULL,
  `fk_i_item_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`s_ip`,`dt_date_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

