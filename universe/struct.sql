CREATE TABLE /*TABLE_PREFIX*/t_universe_files (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED NOT NULL,
    s_slug VARCHAR(250) NULL,
    s_file VARCHAR(250) NOT NULL,
    s_version VARCHAR(14) NULL,
    e_type ENUM('PLUGIN', 'THEME', 'LANGUAGE') NOT NULL DEFAULT 'PLUGIN',
    b_enabled BOOLEAN NOT NULL DEFAULT FALSE,

        PRIMARY KEY (pk_i_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_universe_stats (
    fk_i_universe_id INT UNSIGNED NOT NULL,
    s_hostname VARCHAR(250) NOT NULL,
    s_ip VARCHAR(250) NOT NULL,
    dt_date DATETIME NOT NULL,

        FOREIGN KEY (fk_i_universe_id) REFERENCES /*TABLE_PREFIX*/t_universe_files (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

