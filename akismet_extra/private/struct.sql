CREATE TABLE /*TABLE_PREFIX*/t_item_contact (
  pk_i_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
  fk_i_item_id INT(11) NOT NULL,
  dt_contact DATETIME NOT NULL,
  s_user_email VARCHAR(250) DEFAULT '',
  s_user_name VARCHAR(250) DEFAULT '',
  s_user_phone VARCHAR(250) DEFAULT '',
  s_user_message MEDIUMTEXT,
  s_ip VARCHAR(64) DEFAULT '',
  s_user_agent VARCHAR(250) DEFAULT '',
  b_spam TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_item_send_friend (
  pk_i_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
  fk_i_item_id INT(11) NOT NULL,
  dt_contact DATETIME NOT NULL,
  s_user_email VARCHAR(250) DEFAULT '',
  s_user_name VARCHAR(250) DEFAULT '',
  s_friend_email VARCHAR(250) DEFAULT '',
  s_friend_name VARCHAR(250) DEFAULT '',
  s_user_message MEDIUMTEXT,
  s_ip VARCHAR(64) DEFAULT '',
  s_user_agent VARCHAR(250) DEFAULT '',
  b_spam TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_contact (
  pk_i_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  dt_contact DATETIME NOT NULL,
  s_user_email VARCHAR(250) DEFAULT '',
  s_user_name VARCHAR(250) DEFAULT '',
  s_subject VARCHAR(250) DEFAULT '',
  s_message MEDIUMTEXT,
  s_ip VARCHAR(64) DEFAULT '',
  s_user_agent VARCHAR(250) DEFAULT '',
  b_spam TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';