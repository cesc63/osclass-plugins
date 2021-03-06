CREATE TABLE /*TABLE_PREFIX*/t_market (
    pk_i_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT(10) UNSIGNED NOT NULL,
    s_slug VARCHAR(250) NULL,
    s_banner VARCHAR(250) NULL,
    s_banner_path varchar(250) DEFAULT NULL,
    s_preview VARCHAR(250) NULL,
    i_total_downloads int(10) NOT NULL DEFAULT '0',
    i_ocadmin_downloads int(10) NOT NULL DEFAULT '0',
    b_featured tinyint(1) NOT NULL DEFAULT '0',

        INDEX idx_fk_i_item_id (fk_i_item_id),
        INDEX idx_s_slug (s_slug),
        PRIMARY KEY (pk_i_id),
        CONSTRAINT oc_t_market_ibfk_1 FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_market_files (
    pk_i_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_market_id INT(10) UNSIGNED NOT NULL,
    s_file VARCHAR(250) NOT NULL,
    s_compatible VARCHAR(250) NULL,
    s_compatible_show VARCHAR(250) NULL,
    s_version VARCHAR(14) NULL,
    s_download VARCHAR(250) NULL,
    b_enabled TINYINT(1) NOT NULL DEFAULT FALSE,
    i_total_downloads int(10) NOT NULL DEFAULT '0',
    i_ocadmin_downloads int(10) NOT NULL DEFAULT '0',

        INDEX idx_fk_i_market_id (fk_i_market_id),
        PRIMARY KEY (pk_i_id),
        CONSTRAINT oc_t_market_files_ibfk_1 FOREIGN KEY (fk_i_market_id) REFERENCES /*TABLE_PREFIX*/t_market (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_market_stats (
    fk_i_market_id INT(10) UNSIGNED NOT NULL,
    fk_i_file_id INT(10) UNSIGNED NOT NULL,
    s_hostname VARCHAR(250) NOT NULL,
    s_ip VARCHAR(250) NOT NULL,
    dt_date DATETIME NOT NULL,
    s_osclass_version VARCHAR(6) NULL,

        INDEX idx_fk_i_market_id (fk_i_market_id),
        INDEX idx_fk_i_file_id (fk_i_file_id),
        INDEX idx_dt_date (dt_date),
        CONSTRAINT oc_t_market_stats_ibfk_1 FOREIGN KEY (fk_i_market_id) REFERENCES /*TABLE_PREFIX*/t_market (pk_i_id),
        CONSTRAINT oc_t_market_stats_ibfk_2 FOREIGN KEY (fk_i_file_id) REFERENCES /*TABLE_PREFIX*/t_market_files (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_ip_ranges (
    ip1_temp char(16),
    ip2_temp char(16),
    ip_from int unsigned NOT NULL PRIMARY KEY,
    ip_to int unsigned NOT NULL UNIQUE,
    code char(2) NOT NULL,
    country varchar(100) NOT NULL,
        INDEX (code)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

