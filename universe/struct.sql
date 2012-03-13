CREATE TABLE /*TABLE_PREFIX*/t_universe_files (
    pk_i_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT(10) UNSIGNED NOT NULL,
    s_slug VARCHAR(250) NULL,
    s_file VARCHAR(250) NOT NULL,
    s_version VARCHAR(14) NULL,
    e_type ENUM('PLUGIN', 'THEME', 'LANGUAGE') NOT NULL DEFAULT 'PLUGIN',
    b_enabled TINYINT(1) NOT NULL DEFAULT FALSE,

        INDEX idx_fk_i_item_id (fk_i_item_id),
        INDEX idx_s_slug (s_slug),
        PRIMARY KEY (pk_i_id),
        CONSTRAINT oc_t_universe_files_ibfk_1 FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_universe_stats (
    fk_i_universe_id INT(10) UNSIGNED NOT NULL,
    s_hostname VARCHAR(250) NOT NULL,
    s_ip VARCHAR(250) NOT NULL,
    dt_date DATETIME NOT NULL,

        INDEX idx_fk_i_universe_id (fk_i_universe_id),
        INDEX idx_dt_date (dt_date),
        CONSTRAINT oc_t_universe_stats_ibfk_1 FOREIGN KEY (fk_i_universe_id) REFERENCES /*TABLE_PREFIX*/t_universe_files (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

