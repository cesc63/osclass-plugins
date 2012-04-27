CREATE TABLE /*TABLE_PREFIX*/t_market (
    pk_i_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT(10) UNSIGNED NOT NULL,
    s_slug VARCHAR(250) NULL,

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
    b_enabled TINYINT(1) NOT NULL DEFAULT FALSE,

        INDEX idx_fk_i_market_id (fk_i_market_id),
        PRIMARY KEY (pk_i_id),
        CONSTRAINT oc_t_market_files_ibfk_1 FOREIGN KEY (fk_i_market_id) REFERENCES /*TABLE_PREFIX*/t_market (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_market_stats (
    fk_i_market_id INT(10) UNSIGNED NOT NULL,
    s_hostname VARCHAR(250) NOT NULL,
    s_ip VARCHAR(250) NOT NULL,
    dt_date DATETIME NOT NULL,

        INDEX idx_fk_i_market_id (fk_i_market_id),
        INDEX idx_dt_date (dt_date),
        CONSTRAINT oc_t_market_stats_ibfk_1 FOREIGN KEY (fk_i_market_id) REFERENCES /*TABLE_PREFIX*/t_market (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

