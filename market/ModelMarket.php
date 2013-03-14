<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    /**
     * Model database for Market tables
     *
     * @package OSClass
     * @subpackage Model
     * @since unknown
     */
    class ModelMarket extends DAO {
        /**
         * It references to self object: ModelMarket
         * It is used as a singleton
         *
         * @access private
         * @since unknown
         * @var Currency
         */
        private static $instance ;
        public $pageSize      = 10;
        public $default_sort  = 'update';
        public $default_order = 'desc';

        /**
         * It creates a new ModelMarket object class ir if it has been created
         * before, it return the previous object
         *
         * @access public
         * @since unknown
         * @return Currency
         */
        public static function newInstance() {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Construct
         */
        function __construct() {
            parent::__construct();
            $this->setTableName('t_market') ;
            $this->setPrimaryKey('pk_i_id') ;
            $this->setFields( array('pk_i_id', 'fk_i_item_id', 's_slug', 's_banner_path', 's_banner', 's_preview', 'i_total_downloads', 'i_ocadmin_downloads', 'b_featured') ) ;
        }

        /**
         * Return table name market
         * @return string
         */
        public function getTable() {
            return DB_TABLE_PREFIX.'t_market';
        }

        /**
         * Return table name market files
         * @return string
         */
        public function getTable_Files() {
            return DB_TABLE_PREFIX.'t_market_files';
        }

        /**
         * Return table name market stats
         * @return string
         */
        public function getTable_Stats() {
            return DB_TABLE_PREFIX.'t_market_stats';
        }

        /**
         * Return pageSize
         */
        public function pageSize() {
            return $this->pageSize;
        }

        /**
         * Import sql file
         * @param type $file
         */
        public function import($file) {
            $path = osc_plugin_resource($file) ;
            $sql = file_get_contents($path);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( $this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc() ) ;
            }
        }

        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall() {
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Stats()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Files()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable()) ) ;
            $this->dao->delete(sprintf("%st_plugin_category", DB_TABLE_PREFIX), "s_plugin_name = 'market'");
        }

        public function findByItemId($item_id) {
            $this->dao->select();
            $this->dao->from($this->getTable());
            $this->dao->where('fk_i_item_id', $item_id);
            $this->dao->limit(1);
            $result = $this->dao->get() ;
            if($result!==false) {
                return $result->row() ;
            } else {
                return array();
            }
        }

        /**
         * Get last file from an item
         */
        public function getFileFromItem($item_id) {
            $this->dao->select();
            $this->dao->from($this->getTable()." m , ".$this->getTable_Files()." f ");
            $this->dao->where('m.fk_i_item_id', $item_id);
            $this->dao->where('f.fk_i_market_id = m.pk_i_id');
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $this->dao->limit(1);
            $result = $this->dao->get() ;
            if($result!==false) {
                return $result->row() ;
            } else {
                return array();
            }
        }

        /**
         * Get files from an item
         */
        public function getFilesFromItem($item_id) {
            $this->dao->select();
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "RIGHT");
            $this->dao->where('m.fk_i_item_id', $item_id);
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $result = $this->dao->get() ;
            if($result!==false) {
                return $result->result() ;
            } else {
                return array();
            }
        }

        public function getItemIdBySlug($slug) {
            $this->dao->select('fk_i_item_id');
            $this->dao->from($this->getTable());
            $this->dao->where('s_slug', $slug);
            $this->dao->limit(1);
            $result = $this->dao->get() ;
            if($result!==false) {
                $aux = $result->row() ;
                return $aux['fk_i_item_id'];
            } else {
                return array();
            }
        }

        /**
         * Get file by slug
         *
         */
        public function getFileBySlug($slug, $version = '', $enabled = false) {

            $this->dao->select(
                    " m.s_slug as s_update_url"
                    .", m.s_banner as s_banner"
                    .", m.s_banner_path as s_banner_path"
                    .", m.s_preview as s_preview"
                    .", f.s_file as s_source_file"
                    .", f.s_compatible as s_compatible"
                    .", f.s_version as s_version"
                    .", f.s_download as s_download"
                    .", i.fk_i_category_id as fk_i_category_id"
                    .", i.dt_pub_date as dt_pub_date"
                    .", i.dt_mod_date as dt_mod_date"
                    .", m.i_total_downloads as i_total_downloads"
                    .", i.s_contact_name as s_contact_name"
                    .", m.fk_i_item_id as fk_i_item_id"
                    .", d.s_title as s_title"
                    .", d.s_description as s_description"
                    );
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('m.s_slug', $slug);
            if($enabled) {
                $this->dao->where('f.b_enabled', 1);
            }
            if($version!='') {
                $this->dao->where('f.s_version', $version);
            }
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $this->dao->limit(1);

            $result = $this->dao->get() ;
            if($result!==false) {
                $file = $result->row();
                if( count($file) > 0 ) {
                    $aCategoryPlugins       = explode(',', $this->getCategoriesByType('PLUGIN') );
                    $aCategoryThemes        = explode(',', $this->getCategoriesByType('THEME') );
                    $aCategoryLanguages     = explode(',', $this->getCategoriesByType('LANGUAGE') );

                    if( in_array($file['fk_i_category_id'], $aCategoryThemes) ) {
                        $file['e_type'] = 'THEME';
                    } else if( in_array($file['fk_i_category_id'], $aCategoryLanguages) ) {
                        $file['e_type'] = 'LANGUAGE';
                    } else {
                        $file['e_type'] = 'PLUGIN';
                    }

                    $this->extendWithImages( $file['fk_i_item_id'], $file );

                    unset($file['fk_i_category_id']);
                    unset($file['fk_i_item_id']);

                    return $file;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        private function extendWithImages($itemId, &$file )
        {
            // prepare s_banner_path
            $file['s_banner_path'] = osc_base_url(). $file['s_banner_path'];
            // @todo improve removing call to getResource, it's the same getting
            // the first resource of the call getResources
            $res = ItemResource::newInstance()->getResource( $itemId );
            if($res) {
                if( osc_keep_original_image() ) {
                    $file['s_original'] = osc_base_url().$res['s_path'].$res['pk_i_id']."_original.".$res['s_extension'];
                } else {
                    $file['s_original'] = '';
                }
                $file['s_image']     = osc_base_url().$res['s_path'].$res['pk_i_id'].".".$res['s_extension'];
                $file['s_thumbnail'] = osc_base_url().$res['s_path'].$res['pk_i_id']."_thumbnail.".$res['s_extension'];
            } else {
                $file['s_original']  = '';
                $file['s_image']     = '';
                $file['s_thumbnail'] = '';
            }
            // fill $data[$k]['a_images'] with itemResources, until 6 resources
            $aResources = ItemResource::newInstance()->getResources($itemId, 0, 6);
            foreach($aResources as $_res) {
                if( osc_keep_original_image() ) {
                    $aux['s_original'] = osc_base_url().$_res['s_path'].$_res['pk_i_id']."_original.".$_res['s_extension'];
                } else {
                    $aux['s_original'] = '';
                }
                $aux['s_image']     = osc_base_url().$_res['s_path'].$_res['pk_i_id'].".".$_res['s_extension'];
                $aux['s_thumbnail'] = osc_base_url().$_res['s_path'].$_res['pk_i_id']."_thumbnail.".$_res['s_extension'];
                $file['a_images'][] = $aux;
            }
        }

        private function getCategoriesByType($type = null)
        {
            $catId = array();
            if($type=='THEME') {
                $catId = osc_get_preference('market_categories_theme','market');
            } else if($type=='LANGUAGE') {
                $catId = osc_get_preference('market_categories_languages','market');
            } else if($type=='PLUGIN') {
                $catId = osc_get_preference('market_categories_plugins','market');
            }
            return $catId;
        }
        /*
         * Find market file given an file id
         */
        public function marketFileByPrimaryKey($fileId)
        {
            $result = array();

            if(isset($fileId) && is_numeric($fileId) ) {
                $this->dao->select();
                $this->dao->from( $this->getTable_Files());
                $this->dao->where('pk_i_id', $fileId);

                $result = $this->dao->get() ;
                if($result!==false) {
                    return $result->row();
                }
                return array();
            }
            return $result;
        }

        /**
         * Get file by slug
         *
         * used on download.php
         * used on market.php
         *
         */
        public function getFileForDownloadBySlug($slug, $version = '') {

            $this->dao->select(
                    " f.fk_i_market_id as fk_i_market_id"
                    .", f.pk_i_id as pk_i_id"
                    .", f.s_file as s_source_file"
                    .", f.s_download as s_download"
                    .", m.s_slug as s_update_url"
                    .", f.s_version "
                    );
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->where('m.s_slug', $slug);
            $this->dao->where('f.b_enabled', 1);
            if($version!='') {
                $this->dao->where('f.s_version', $version);
            }
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $this->dao->limit(1);
            $result = $this->dao->get() ;
            if($result!==false) {
                return $result->row();
            } else {
                return array();
            }
        }

        /**
         * Check if the file already exists in the market
         */
        public function marketExists($item_id) {
            $this->dao->select();
            $this->dao->from($this->getTable());
            $this->dao->where('fk_i_item_id', $item_id);
            $result = $this->dao->get() ;
            if($result!==false) {
                $row = $result->row();
                if(empty($row)) {
                    return false;
                } else {
                    return $row['pk_i_id'];
                };
            } else {
                return false;
            }
        }

        /**
         * Insert new market
         */
        public function insertMarket($item_id, $slug, $featured, $preview = '') {
            $this->dao->insert($this->getTable(),array('fk_i_item_id' => $item_id, 's_slug' => $slug, 'b_featured' => $featured, 's_preview' => $preview));
            return $this->dao->insertedId();
        }

        /**
         * Insert new file
         */
        public function insertFile($market_id, $path, $download_url, $version, $comp_versions, $enabled = 1) {
            $versions = array();
            if($comp_versions!='') {
                foreach($comp_versions as $k => $v) {
                    $versions[] = $k;
                };
            }
            return $this->dao->insert( $this->getTable_Files(), array(
                'fk_i_market_id' => $market_id,
                's_file' => $path,
                's_download' => $download_url,
                's_version' => $version,
                's_compatible' => implode(",", $versions),
                'b_enabled' => $enabled
            ));
        }

        /**
         * Update a file
         */
        public function updateFile($market_id, $pk_i_id, $aSet) {
            $return = $this->dao->update( $this->getTable_Files(), $aSet, 'pk_i_id = '.$pk_i_id.' AND fk_i_market_id = '.$market_id);
            if($return === false) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Update a file
         */
        public function featuredOn($item_id) {
            $return = $this->dao->update($this->getTable(),
                    array('b_featured'      => '1'),
                    array('fk_i_item_id'    => $item_id));

            if($return === false) {
                return false;
            } else {
                return true;
            }
        }
        public function featuredOff($item_id) {
            $return = $this->dao->update($this->getTable(),
                    array('b_featured'      => '0'),
                    array('fk_i_item_id'    => $item_id));
            if($return === false) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Delete a file
         */
        public function deleteFile($id, $item, $secret) {
            if(!is_numeric($id) || !is_numeric($item)) {
                return false;
            }

            $this->dao->select();
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->where('m.fk_i_item_id', $item);
            $this->dao->where('f.pk_i_id', $id);
            $result = $this->dao->get() ;

            if($result!==false && $result->numRows()==0) {
                return false;
            }

            $item = Item::newInstance()->findByPrimaryKey($item);

            if($secret!=$item['s_secret']) {
                return false;
            }

            $this->dao->delete($this->getTable_Stats(), 'fk_i_file_id = '.$id);
            return $this->dao->delete($this->getTable_Files(), 'pk_i_id = '.$id);
        }

        /**
         * Remove market banner from db and from HD
         *
         * @param type $item_id
         * @param type $secret
         */
        public function removeBannerFile( $item_id, $secret )
        {
            $this->dao->select();
            $this->dao->from($this->getTable()." m");
            $this->dao->where('m.fk_i_item_id', $item_id);
            $result = $this->dao->get() ;

            if($result!==false && $result->numRows()==0) {
                return false;
            }
            $market = $result->row();

            $item = Item::newInstance()->findByPrimaryKey($item_id);

            if($secret!=$item['s_secret']) {
                return false;
            }
            $banner_path = CONTENT_PATH . 'uploads/market/' . $market['s_banner'];
            // remove banner
            $this->dao->update( $this->getTable(), array('s_banner' => NULL) );
            if( unlink($banner_path) ) {
                return true;
            }
            return false;
        }

        /*
         * Check if slug exists or not
         */
        public function checkSlug($slug, $item_id = null) {
            $this->dao->select();
            $this->dao->from($this->getTable());
            if($item_id!=null) {
                $this->dao->where('fk_i_item_id != '. $item_id);
            }
            $this->dao->where('s_slug', $slug);
            $result = $this->dao->get() ;
            if($result!==false && $result->numRows()>0) {
                return false;
            } else {
                return true;
            }
        }

        /*
         * Insert new stat about a download
         */
        public function insertStat($market_id, $file_id, $remote_host, $remote_addr, $osclass_version) {

            $aSet = array(
                'fk_i_market_id'    => $market_id,
                'fk_i_file_id'      => $file_id,
                's_hostname'        => $remote_host,
                's_ip'              => $remote_addr,
                'dt_date'           => date('Y-m-d H:i:s'),
                's_osclass_version' => $osclass_version
                );

            // get s_country_code from ip
            $result         = $this->dao->query("select code from ".DB_TABLE_PREFIX."t_ip_ranges where ip_from <= INET_ATON('$remote_addr') AND ip_to >= INET_ATON('$remote_addr') LIMIT 1 ");
            $aux            = $result->row();
            $s_country_code = '';
            if($aux!==false && isset($aux['code']) ){
                $aSet['s_country_code'] = $aux['code'];
            }

            // insert market stat
            $this->dao->insert($this->getTable_Stats(), $aSet);

            // insert market stat ++
            $this->increaseMarketDownload($market_id, $osclass_version);
            // insert market file stat ++
            $this->increaseMarketFileDownload($file_id, $osclass_version);
        }

        /**
         * Increment Market item downloads
         *
         * @param type $marketId
         * @return type
         */
        public function increaseMarketDownload($id, $osclass_version)
        {
            if(!is_numeric($id)) {
                return false;
            }
            if($osclass_version=='') {
                $sql = sprintf('UPDATE %s SET i_total_downloads = i_total_downloads + 1 WHERE pk_i_id = %d', $this->getTable(), $id);
            } else {
                $sql = sprintf('UPDATE %s SET i_total_downloads = i_total_downloads + 1, i_ocadmin_downloads = i_ocadmin_downloads + 1 WHERE pk_i_id = %d', $this->getTable(), $id);
            }
            return $this->dao->query($sql);
        }

        /**
         * Increment Market-file downloads
         *
         * @param type $fileId
         * @return type
         */
        public function increaseMarketFileDownload($id, $osclass_version)
        {
            if(!is_numeric($id)) {
                return false;
            }

            if($osclass_version=='') {
                $sql = sprintf('UPDATE %s SET i_total_downloads = i_total_downloads + 1 WHERE pk_i_id = %d', $this->getTable_Files(), $id);
            } else {
                $sql = sprintf('UPDATE %s SET i_total_downloads = i_total_downloads + 1, i_ocadmin_downloads = i_ocadmin_downloads + 1 WHERE pk_i_id = %d', $this->getTable_Files(), $id);
            }
            return $this->dao->query($sql);
        }

        /*
         * Get latest files added
         */
        public function getLatest($page = 0) {
            $this->page = 20;
            return $this->getData('LATEST', $page);
        }

        /*
         * Disable a file
         */
        public function disable($uid) {
            if(is_numeric($uid)) {
                return $this->dao->update( $this->getTable_Files(), array('b_enabled' => 0), 'pk_i_id = '.$uid);
            }
            return false;
        }

        /*
         * Enable a file
         */
        public function enable($uid) {
            if(is_numeric($uid)) {
                return $this->dao->update( $this->getTable_Files(), array('b_enabled' => 1), 'pk_i_id = '.$uid);
            }
            return false;
        }

        /*
         * Delete a file
         *
         * DELETE ? TODO
         */
        public function delete($uid) {
            if(is_numeric($uid)) {
                return $this->dao->delete($this->getTable_Files(), 'pk_i_id = '.$uid);
            }
            return false;
        }
        /**
         *
         * @param type $id ITEM ID
         * @return type
         */
        public function deleteMarket($id) {
            return $this->dao->delete($this->getTable(), 'fk_i_item_id = '.$id);
        }

        public function deleteMarket_file($id) {
            return $this->dao->delete($this->getTable_Files(), 'pk_i_id = '.$id);
        }

        public function deleteMarket_stat($id) {
            return $this->dao->delete($this->getTable_Stats(), 'fk_i_file_id = '.$id);
        }

        /*
         * Get downloads
         *
         * @todo improve getting data form table_view
         */
        public function getDownloads($uid) {

            $this->dao->select('COUNT(*) as total');
            $this->dao->from($this->getTable_Stats());
            $this->dao->where('fk_i_market_id', $uid);
            $this->dao->groupBy('fk_i_market_id');
            $result = $this->dao->get() ;
            if($result!==false) {
                $row = $result->result();
                return $row[0]['total'];
            } else {
                return 0;
            }
        }

        public function getDownloadsByCountry($type = 'all', $from_date = '', $ocadmin = false)
        {
            $this->dao->select('s_country_code, COUNT(*) as num');
            $this->dao->from(DB_TABLE_PREFIX."t_market_stats") ;
            $this->dao->join(DB_TABLE_PREFIX.'t_market', DB_TABLE_PREFIX.'t_market.pk_i_id = '.DB_TABLE_PREFIX.'t_market_stats.fk_i_market_id');
            $this->dao->join(DB_TABLE_PREFIX.'t_item', DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_market.fk_i_item_id');
            if($from_date!='') {
                $this->dao->where("dt_date > '$from_date' ") ;
            }

            if($ocadmin) {
                $this->dao->where(DB_TABLE_PREFIX.'t_market_stats.s_osclass_version <> "" ');
            } else {
                $this->dao->where(DB_TABLE_PREFIX.'t_market_stats.s_osclass_version = "" ' );
            }

            $this->dao->groupBy('s_country_code') ;

             $aCategory = null;
             if($type == 'plugins') {
                // get plugin categories ....
                $aCategory    = osc_get_preference('market_categories_plugins','market');
            } else if($type == 'themes') {
                // get themes categories ...
                $aCategory    = osc_get_preference('market_categories_theme','market');
            } else if($type == 'languages') {
                // get languages categories ...
                $aCategory    = osc_get_preference('market_categories_languages','market');
            }

            if($aCategory != null) {
                $this->dao->where(DB_TABLE_PREFIX.'t_item.fk_i_category_id IN ('.$aCategory.') ');
            }

            error_log($this->dao->_getSelect());
            $result = $this->dao->get();
            if($result!==false) {
                return $result->result();
            }
            return array();
        }

        /*
         * Get download stats, 10 month, 10 week, 10 day
         * $type = all , plugins, themes, languages
         */
        public function getAllStats($from_date, $date = 'day', $type = 'all', $item_id = '')
        {
            // sub query
            $this->dao->select('dt_date, fk_i_market_id') ;
            $this->dao->from($this->getTable_Stats());
            $this->dao->where("dt_date > '$from_date'") ;
            $sql_total_downloads = '('.$this->dao->_getSelect().') as a ';

            $this->dao->where('s_osclass_version <> ""');
            $sql_ocadmin_downloads = '('.$this->dao->_getSelect().') as a ';
            $this->dao->_resetSelect();

            if($item_id != '') { $type='all'; }
            if($date=='week') {
                $this->dao->select('dt_date as date_time, WEEK(dt_date) as d_date, COUNT(1) as num') ;
                $this->dao->groupBy('WEEK(a.dt_date)') ;
            } else if($date=='month') {
                $this->dao->select('dt_date as date_time, MONTHNAME(dt_date) as d_date, COUNT(1) as num') ;
                $this->dao->groupBy('MONTH(a.dt_date)') ;
            } else {
                $this->dao->select('dt_date as date_time, DATE(dt_date) as d_date, COUNT(1) as num') ;
                $this->dao->groupBy('DAY(a.dt_date)') ;
            }

            $this->dao->from($sql_total_downloads);
            $this->dao->join(DB_TABLE_PREFIX.'t_market', DB_TABLE_PREFIX.'t_market.pk_i_id = a.fk_i_market_id');
            $this->dao->join(DB_TABLE_PREFIX.'t_item', DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_market.fk_i_item_id');

            $aCategory = null;
            if($type == 'all') {
                // nothing todo
                if($item_id != '') {
                    $this->dao->where(DB_TABLE_PREFIX.'t_item.pk_i_id = '.$item_id);
                }
            } else if($type == 'plugins') {
                // get plugin categories ....
                $aCategory    = osc_get_preference('market_categories_plugins','market');
            } else if($type == 'themes') {
                // get themes categories ...
                $aCategory    = osc_get_preference('market_categories_theme','market');
            } else if($type == 'languages') {
                // get languages categories ...
                $aCategory    = osc_get_preference('market_categories_languages','market');
            }

             if($aCategory != null) {
                $this->dao->where(DB_TABLE_PREFIX.'t_item.fk_i_category_id IN ('.$aCategory.') ');
            }

            $this->dao->orderBy('a.dt_date', 'DESC') ;

            // get ocadmin downloads
            $result         = $this->dao->get() ;
            $array_total    = $result->result() ;

            // get total downloads
            $this->dao->_resetSelect();

            // Get i_ocadmin_downloads

            if($date=='week') {
                $this->dao->select('WEEK(dt_date) as d_date, COUNT(1) as num') ;
                $this->dao->groupBy('WEEK(a.dt_date)') ;
            } else if($date=='month') {
                $this->dao->select('MONTHNAME(dt_date) as d_date, COUNT(1) as num') ;
                $this->dao->groupBy('MONTH(a.dt_date)') ;
            } else {
                $this->dao->select('DATE(dt_date) as d_date, COUNT(1) as num') ;
                $this->dao->groupBy('DAY(a.dt_date)') ;
            }

            $this->dao->from($sql_ocadmin_downloads);
            $this->dao->join(DB_TABLE_PREFIX.'t_market', DB_TABLE_PREFIX.'t_market.pk_i_id = a.fk_i_market_id');
            $this->dao->join(DB_TABLE_PREFIX.'t_item', DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_market.fk_i_item_id');

            $aCategory = null;
            if($type == 'all') {
                // nothing todo
                if($item_id != '') {
                    $this->dao->where(DB_TABLE_PREFIX.'t_item.pk_i_id = '.$item_id);
                }
            } else if($type == 'plugins') {
                // get plugin categories ....
                $aCategory    = osc_get_preference('market_categories_plugins','market');
            } else if($type == 'themes') {
                // get themes categories ...
                $aCategory    = osc_get_preference('market_categories_theme','market');
            } else if($type == 'languages') {
                // get languages categories ...
                $aCategory    = osc_get_preference('market_categories_languages','market');
            }

            if($aCategory != null) {
                $this->dao->where(DB_TABLE_PREFIX.'t_item.fk_i_category_id IN ('.$aCategory.') ');
            }

            $this->dao->orderBy('a.dt_date', 'DESC') ;


            $result         = $this->dao->get() ;


            $array_ocadmin  = $result->result() ;

            // prepare arrays
            $_array_total   = array();
            $_array_ocadmin = array();

            foreach($array_total as $aux) {
                $_array_total[$aux['d_date']] = $aux['num'];
            }

            foreach($array_ocadmin as $aux) {
                $_array_ocadmin[$aux['d_date']] = $aux['num'];
            }


            // merge arrays
            $array_merge = array();
            foreach($_array_total as $key => $aux) {
                $_aux = 0;
                if(isset($_array_ocadmin[$key])) {
                    $_aux = $_array_ocadmin[$key];
                }

                $array_merge[] = array(
                    'd_date'        => $key,
                    'num'           => $_array_total[$key],
                    'ocadmin_num'   => $_aux
                );
            }

//            error_log( print_r($array_merge , true) );

            return $array_merge;
        }

        /*
         * Get download stats, 10 month, 10 week, 10 day
         * $type = all , plugins, themes, languages
         * $from_date -> datetime
         *
         */
        public function getTop($from_date, $type = 'all', $limit = 10)
        {
            // sub select
            $this->dao->select(DB_TABLE_PREFIX.'t_market_stats.fk_i_market_id as pk_i_id') ;
            $this->dao->from(DB_TABLE_PREFIX . 't_market_stats');
            $this->dao->where(DB_TABLE_PREFIX . "t_market_stats.dt_date > '$from_date'");
            $subquery = '('. $this->dao->_getSelect() . ') as a ';
            $this->dao->_resetSelect();

            $this->dao->select('count(*) as total, '.DB_TABLE_PREFIX.'t_item.pk_i_id, '.DB_TABLE_PREFIX.'t_item.fk_i_category_id');
            $this->dao->from($subquery);
            $this->dao->join(DB_TABLE_PREFIX.'t_market', DB_TABLE_PREFIX.'t_market.pk_i_id = a.pk_i_id');
            $this->dao->join(DB_TABLE_PREFIX.'t_item', DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_market.fk_i_item_id' );

            $aCategory = null;
            if($type == 'plugins') {
                // get plugin categories ....
                $aCategory    = osc_get_preference('market_categories_plugins','market');
            } else if($type == 'themes') {
                // get themes categories ...
                $aCategory    = osc_get_preference('market_categories_theme','market');
            } else if($type == 'languages') {
                // get languages categories ...
                $aCategory    = osc_get_preference('market_categories_languages','market');
            }

            if($aCategory != null) {
                $this->dao->where(DB_TABLE_PREFIX.'t_item.fk_i_category_id IN ('.$aCategory.') ');
            }

            $this->dao->groupBy('a.pk_i_id');
            $this->dao->orderBy('total', 'DESC') ;
            $this->dao->limit(0, $limit);

            $result = $this->dao->get() ;
            return Item::newInstance()->extendData( $result->result() );
        }

        /**
         * t_market
         * Update i_total_downloads and i_ocadmin_downloads, using market_stats
         * table .
         */
        function recountMarketStats() {

            $ocadmin_downloads = '';
            $this->dao->select('count(*) ocadmin_downloads');
            $this->dao->from($this->getTable_Stats());
            $this->dao->where($this->getTable_Stats() . '.s_osclass_version <> ""');
            $this->dao->where($this->getTable_Stats() . '.fk_i_market_id = market_id');
            $this->dao->groupBy($this->getTable_Stats() . '.fk_i_market_id');
            $sub_ocadmin_downloads = $this->dao->_getSelect();
            $this->dao->_resetSelect() ;

            $total_downloads = '';
            $this->dao->select('count(*) total_downloads');
            $this->dao->from($this->getTable_Stats());
            $this->dao->where($this->getTable_Stats() . '.fk_i_market_id = market_id');
            $this->dao->groupBy($this->getTable_Stats() . '.fk_i_market_id');
            $sub_total_downloads = $this->dao->_getSelect();
            $this->dao->_resetSelect() ;

            $this->dao->select('pk_i_id as market_id');
            $this->dao->select('('.$sub_ocadmin_downloads.') as ocadmin_downloads');
            $this->dao->select('('.$sub_total_downloads.') as total_downloads');
            $this->dao->from($this->getTable());

            $result = $this->dao->get() ;
            $array = $result->result();

            // update values
            foreach($array as $aux) {
                $id  = $aux['market_id'];
                $osclass_downloads = $aux['ocadmin_downloads'];
                $total_downloads   = $aux['total_downloads'];

                $res = $this->dao->update(
                        $this->getTable(),
                        array('i_ocadmin_downloads'  => $osclass_downloads,
                              'i_total_downloads'    => $total_downloads),
                        array('pk_i_id' => $id));
                if($res!==FALSE) {

                }
            }
        }

        /**
         * t_market_files
         * Update i_total_downloads and i_ocadmin_downloads, using market_stats
         * table .
         */
        function recountMarketFilesStats() {
             $ocadmin_downloads = '';
            $this->dao->select('count(*) ocadmin_downloads');
            $this->dao->from($this->getTable_Stats());
            $this->dao->where($this->getTable_Stats() . '.s_osclass_version <> ""');
            $this->dao->where($this->getTable_Stats() . '.fk_i_file_id = file_id');
            $this->dao->groupBy($this->getTable_Stats() . '.fk_i_file_id');
            $sub_ocadmin_downloads = $this->dao->_getSelect();
            $this->dao->_resetSelect() ;

            $total_downloads = '';
            $this->dao->select('count(*) total_downloads');
            $this->dao->from($this->getTable_Stats());
            $this->dao->where($this->getTable_Stats() . '.fk_i_file_id = file_id');
            $this->dao->groupBy($this->getTable_Stats() . '.fk_i_file_id');
            $sub_total_downloads = $this->dao->_getSelect();
            $this->dao->_resetSelect() ;

            $this->dao->select('pk_i_id as file_id');
            $this->dao->select('('.$sub_ocadmin_downloads.') as ocadmin_downloads');
            $this->dao->select('('.$sub_total_downloads.') as total_downloads');
            $this->dao->from($this->getTable_Files());

            $result = $this->dao->get() ;
            $array = $result->result();

            // update values
            foreach($array as $aux) {
                $id  = $aux['file_id'];
                $osclass_downloads = $aux['ocadmin_downloads'];
                $total_downloads   = $aux['total_downloads'];

                $res = $this->dao->update(
                        $this->getTable_Files(),
                        array('i_ocadmin_downloads'  => $osclass_downloads,
                              'i_total_downloads'    => $total_downloads),
                        array('pk_i_id' => $id));
                if($res!==FALSE) {

                }
            }
        }

        /*
         * Get plugins
         */
        public function getPlugins($page = 0, $pageSize = '', $sort = null, $order = null) {
            if($sort==null) {
                $sort = $this->default_sort;
            }
            if($order==null) {
                $order = $this->default_order;
            }
            return $this->getData('PLUGIN', $page, $pageSize, $sort, $order);
        }

        /*
         * Get themes
         */
        public function getThemes($page = 0, $pageSize = '', $sort = null, $order = null) {
            if($sort==null) {
                $sort = $this->default_sort;
            }
            if($order==null) {
                $order = $this->default_order;
            }
            return $this->getData('THEME', $page, $pageSize, $sort, $order);
        }

        /*
         * Get languages
         */
        public function getLanguages($page = 0, $pageSize = '', $sort = null, $order = null) {
            if($sort==null) {
                $sort = $this->default_sort;
            }
            if($order==null) {
                $order = $this->default_order;
            }
            return $this->getData('LANGUAGE', $page, $pageSize, $sort, $order);
        }

        /*
         * Get search results
         */
        public function getSearch($pattern = '', $page = 0, $type = '') {
            return array();
        }

        /**
         * Manage Market table, show info market given a page and $start - $limit
         * NOTE: $page [1,2,3,...,N]
         *
         * @param type $page
         * @param type $limit
         * @param type $aInfo
         * @return string
         */
        public function manageMarket($page, $limit, $aInfo = null)
        {
            $type = '';
            if($page>0) {
                $page--;
            }
            if($limit == null) {
                $start = $page*$this->pageSize;
                $limit = $this->pageSize;
            } else {
                $start = $page*$limit;
            }
            $this->dao->select(
                    "SQL_CALC_FOUND_ROWS m.pk_i_id as pk_i_id"
                    .", m.s_slug as s_update_url"
                    .", m.s_banner as s_banner"
                    .", m.s_banner_path as s_banner_path"
                    .", m.s_preview as s_preview"
                    .", i.fk_i_category_id as fk_i_category_id"
                    .", i.dt_pub_date as dt_pub_date"
                    .", i.dt_mod_date as dt_mod_date"
                    .", i.s_contact_name as s_contact_name"
                    .", m.fk_i_item_id as fk_i_item_id"
                    .", d.s_title as s_title"
                    .", d.s_description as s_description"
                    );
            $this->dao->from($this->getTable()." m");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            if( isset($aInfo['user_id']) && is_numeric($aInfo['user_id']) ) {
                $this->dao->where('i.fk_i_user_id', (int)$aInfo['user_id']);
            }
            $this->dao->orderBy('m.pk_i_id', 'DESC');
            $this->dao->limit($start, $limit);

            $result = $this->dao->get() ;

            if($result!==false) {

                $data = $result->result();
                // get total rows
                $result_count = $this->dao->query("SELECT FOUND_ROWS() as total");
                $aux = $result_count->row();
                $total = $aux['total'];

                foreach($data as $k => $v) {
                    $data[$k]['e_type'] = $type;
                    $this->extendWithImages( $v['fk_i_item_id'], $data[$k]);
                }

                // prepare for pass to view
                $newData['iTotalRecords']        = count($data);
                $newData['iTotalDisplayRecords'] = $total ;
                $newData['iDisplayLength']       = $limit;
                $newData['aaData']               = $data;

                return $newData;
            } else {
                return array();
            }
        }

        /**
         * Get featured market items, ordered by dt_mod_date, given a type
         * type = (plugins, themes, languages)
         *
         * @param type $type
         */
        public function getFeatured($type, $num)
        {
            $catId = $this->getCategoriesByType($type);

            $this->dao->select(
                    "f.pk_i_id as pk_i_id"
                    .", m.s_slug as s_update_url"
                    .", m.s_banner as s_banner"
                    .", m.s_banner_path as s_banner_path"
                    .", m.s_preview as s_preview"
                    .", f.s_file as s_source_file"
                    .", f.s_compatible as s_compatible"
                    .", f.s_version as s_version"
                    .", f.s_download as s_download"
                    .", i.fk_i_category_id as fk_i_category_id"
                    .", i.dt_pub_date as dt_pub_date"
                    .", i.dt_mod_date as dt_mod_date"
                    .", i.s_contact_name as s_contact_name"
                    .", m.fk_i_item_id as fk_i_item_id"
                    .", d.s_title as s_title"
                    .", d.s_description as s_description"
                    .", m.i_total_downloads as i_total_downloads"
                    .", m.b_featured as b_featured"
                    );
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('f.b_enabled', 1);
            $this->dao->where('m.b_featured', 1);
            if($catId!=null) {
                $this->dao->where('i.fk_i_category_id IN ('. $catId .')' );
            }
            // ?? order by pub data, mod date
//            $this->dao->orderBy('i.dt_pub_date, i.dt_mod_date', 'DESC');
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $subquery = $this->dao->_getSelect() ;
            $this->dao->_resetSelect() ;

            // group by item_id, and sort
            $this->dao->select();
            $this->dao->from($this->getTable_Files());
            $this->dao->join(sprintf( '(%s) as aux', $subquery ), "aux.pk_i_id = ".DB_TABLE_PREFIX."t_market_files.pk_i_id ", "RIGHT");
            $this->dao->groupBy($this->getTable_Files().".fk_i_market_id");
            $this->dao->orderBy(DB_TABLE_PREFIX.'t_market_files.pk_i_id', 'DESC');
            $this->dao->limit(0, $num);

            $result = $this->dao->get() ;

            if($result!==false) {
                $data = $result->result();

                foreach($data as $k => $v) {
                    $data[$k]['e_type'] = $type;
                    // clean description
                    $aux_description = $data[$k]['s_description'];
                    $data[$k]['s_description'] = nl2br($aux_description);

                    $this->extendWithImages($v['fk_i_item_id'], $data[$k]);

                    unset($data[$k]['fk_i_item_id']);
                    unset($data[$k]['fk_i_category_id']);
                    unset($data[$k]['pk_i_id']);
                }
                return $data;
            } else {
                return array();
            }
        }


        /*
         * General purpouse function
         */
        public function getData($type = 'LATEST', $page = 0, $pageSize = '' , $sort = 'updated', $order = 'desc' ) {

            if($pageSize!='' && is_numeric($pageSize)) {
                $this->pageSize = $pageSize;
            }
            $catId = $this->getCategoriesByType($type);
            if( $catId == array() ) {
                $type   = '';
                $catId  = null;
            }

            $start = $page*$this->pageSize;
            $this->dao->select(
                    "f.pk_i_id as pk_i_id"
                    .", m.s_slug as s_update_url"
                    .", m.s_banner as s_banner"
                    .", m.s_banner_path as s_banner_path"
                    .", m.s_preview as s_preview"
                    .", f.s_file as s_source_file"
                    .", f.s_compatible as s_compatible"
                    .", f.s_version as s_version"
                    .", f.s_download as s_download"
                    .", i.fk_i_category_id as fk_i_category_id"
                    .", i.dt_pub_date as dt_pub_date"
                    .", i.dt_mod_date as dt_mod_date"
                    .", i.s_contact_name as s_contact_name"
                    .", m.fk_i_item_id as fk_i_item_id"
                    .", d.s_title as s_title"
                    .", d.s_description as s_description"
                    .", m.i_total_downloads as i_total_downloads"
                    .", m.b_featured as b_featured"
                    );
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('f.b_enabled', 1);
            if($catId!=null) {
                $this->dao->where('i.fk_i_category_id IN ('. $catId .')' );
            }

            $this->dao->orderBy('f.pk_i_id', 'DESC');

            $subquery = $this->dao->_getSelect() ;
            $this->dao->_resetSelect() ;


            $this->dao->select();
            $this->dao->from($this->getTable_Files());
            $this->dao->join(sprintf( '(%s) as aux', $subquery ), "aux.pk_i_id = ".DB_TABLE_PREFIX."t_market_files.pk_i_id ", "RIGHT");
            // order
            if($sort == 'downloads') {
                $this->dao->orderBy('aux.i_total_downloads', $order);
            } else if($sort == 'updated') {
                $this->dao->orderBy('aux.dt_mod_date', $order);
            } else {
                $this->dao->orderBy('aux.dt_mod_date', $order);
            }

            $this->dao->groupBy($this->getTable_Files().".fk_i_market_id");
            $this->dao->limit($start, $this->pageSize);


//            error_log('->>>>');
//            error_log( $this->dao->_getSelect() );

            $result = $this->dao->get() ;

            if($result!==false) {
                $data = $result->result();

                foreach($data as $k => $v) {
                    $data[$k]['e_type'] = $type;
                    // clean description
                    $aux_description = $data[$k]['s_description'];
                    $data[$k]['s_description'] = ($aux_description);

                    $this->extendWithImages($v['fk_i_item_id'], $data[$k]);

                    unset($data[$k]['fk_i_item_id']);
                    unset($data[$k]['fk_i_category_id']);
                    unset($data[$k]['pk_i_id']);
                }
                return $data;
            } else {
                return array();
            }
        }

        /**
         * Get random items given a type market file
         *
         * @param type $exclude_item_id
         * @param string $type
         * @param type $num
         * @return type
         */
        public function getRandom($exclude_item_id = null, $type = 'LATEST', $num = 3)
        {
            $catId = $this->getCategoriesByType($type);
            if( $catId == array() ) {
                $type   = '';
                $catId  = null;
            }

            $this->dao->select(
                    "DISTINCT i.pk_i_id as pk_i_id"
                    .", m.s_slug as s_update_url"
                    .", m.s_banner as s_banner"
                    .", m.s_banner_path as s_banner_path"
                    .", m.s_preview as s_preview"
                    .", f.s_file as s_source_file"
                    .", f.s_compatible as s_compatible"
                    .", f.s_version as s_version"
                    .", f.s_download as s_download"
                    .", i.fk_i_category_id as fk_i_category_id"
                    .", i.dt_pub_date as dt_pub_date"
                    .", i.dt_mod_date as dt_mod_date"
                    .", i.s_contact_name as s_contact_name"
                    .", m.fk_i_item_id as fk_i_item_id"
                    .", d.s_title as s_title"
                    .", d.s_description as s_description"
                    );
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('f.b_enabled = 1');
            if($catId!=null) {
                $this->dao->where('i.fk_i_category_id IN ('. $catId .')' );
            }
            if($exclude_item_id != null ) {
                $this->dao->where('i.pk_i_id NOT IN ('. $exclude_item_id.')');
            }
            $this->dao->groupBy('pk_i_id');
            $this->dao->orderBy('RAND()');
            $this->dao->limit( 0, $num);

            $result = $this->dao->get() ;
            if($result===false) {
                return array();
            }

            return $result->result();
        }
        /*
         * General purpouse function
         */
        public function countData($type = 'PLUGIN')
        {
            $catId = $this->getCategoriesByType($type);
            if( $catId == array() ) {
                return array();
            }

            // get languages categories ...
            $this->dao->select('m.pk_i_id');
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('f.b_enabled', 1);
            // in category
            $this->dao->where('i.fk_i_category_id IN ('.$catId.')');
            $this->dao->groupBy("m.pk_i_id");

            $result = $this->dao->get() ;

            if($result!==false) {
                return $result->numRows();
            } else {
                return 0;
            }
        }

        /**
         * private function getCounty code
         *
         * @return string country code
         */
        function _getCountryCode($ip = '')
        {
            if($ip=='') {
                $ip = get_ip();
            }

            $aIP   = explode('.', $IP);

            if( count($aIP) != 4 ) {
                return false;
            }

            $numIP     = (16777216 * $aIP[0]) + (65536 * $aIP[1]) + (256 * $aIP[2]) + $aIP[3];

            $this->dao->select();
            $this->dao->from(DB_TABLE_PREFIX . 't_ip_range');
            $this->dao->where('ip_from <=', $numIP);
            $this->dao->where('ip_to >=', $numIP);
            $this->dao->limit(1);

            $result   = $this->dao->get();
            if($result!==FALSE) {
                $aux = $result->row();
                return $aux['code'];
            }
            return '';
        }

    }

?>
