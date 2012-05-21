CREATE TABLE /*TABLE_PREFIX*/t_subscribers (
    pk_i_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    s_email VARCHAR(100) NOT NULL,
    b_active TINYINT(1) NOT NULL DEFAULT 1,
    d_subscription DATE DEFAULT NULL,
    d_unsubscribe DATE DEFAULT NULL,
    c_ip char(64) NOT NULL default '0.0.0.0',

        PRIMARY KEY (pk_i_id),
        UNIQUE KEY (s_email)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';