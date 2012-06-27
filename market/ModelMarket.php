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
        public $pageSize = 10;

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
            $this->setFields( array('pk_i_id', 'fk_i_item_id', 's_slug', 's_banner', 's_preview') ) ;
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
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->where('m.fk_i_item_id', $item_id);
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $result = $this->dao->get() ;
            if($result!==false) {
                return $result->result() ;
            } else {
                return array();
            }
        } 
        
        /**
         * Get file by slug
         */
        public function getFileBySlug($slug, $version = '') {
            
            $this->dao->select();
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('m.s_slug', $slug);
            $this->dao->where('f.b_enabled', 1);
            if($version!='') {
                $this->dao->where('f.s_version', $version);
            }
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $this->dao->limit(1);
            $result = $this->dao->get() ;
            if($result!==false) {
                $file = $result->row();
                if($file['fk_i_category_id']==96) {
                    $file['e_type'] = 'THEME';
                } else if($file['fk_i_category_id']==98) {
                    $file['e_type'] = 'LANGUAGE';
                } else {
                    $file['e_type'] = 'PLUGIN';
                }

                $res = ItemResource::newInstance()->getResource($file['fk_i_item_id']);
                if($res) {
                    $file['s_image'] = osc_base_url().$res['s_path'].$res['pk_i_id'].".".$res['s_extension'];
                    $file['s_thumbnail'] = osc_base_url().$res['s_path'].$res['pk_i_id']."_thumbnail.".$res['s_extension'];
                } else {
                    $file['s_image'] = '';
                    $file['s_thumbnail'] = '';
                }
                unset($file['s_contact_email']);
                
                return $file;
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
        public function insertMarket($item_id, $slug, $preview = '') {
            $this->insert(array('fk_i_item_id' => $item_id, 's_slug' => $slug, 's_preview' => $preview));
            return $this->dao->insertedId();
        }
        
        /**
         * Insert new file
         */
        public function insertFile($market_id, $path, $download_url, $version, $comp_versions) {
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
                'b_enabled' => 1
            ));
        }
       
        /**
         * Update a file
         */
        public function updateFile($market_id, $pk_i_id, $aSet) {
            return $this->dao->update( $this->getTable_Files(), $aSet, 'pk_i_id = '.$pk_i_id.' AND fk_i_market_id = '.$market_id);
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
        
        /*
         * Check if slug exists or not
         */
        public function checkSlug($slug, $item_id) {
            $this->dao->select();
            $this->dao->from($this->getTable());
            $this->dao->where('fk_i_item_id != '. $item_id);
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
        public function insertStat($market_id, $remote_host, $remote_addr) {
            return $this->dao->insert($this->getTable_Stats(), array(
                'fk_i_market_id' => $market_id,
                's_hostname' => $remote_host,
                's_ip' => $remote_addr,
                'dt_date' => date('Y-m-d H:i:s')
            ));
        }
        
        /*
         * Get latest files added
         */
        public function getLatest($page = 0) {
            $this->dao->select();
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->orderBy('m.pk_i_id', 'DESC');
            $this->dao->limit($page, 20);
            $result = $this->dao->get() ;
            if($result!==false) {
                return $result->result() ;
            } else {
                return array();
            }
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
                $res = $this->dao->update( $this->getTable_Files(), array('b_enabled' => 1), 'pk_i_id = '.$uid);
            }
            return false;
        }
       
        /*
         * Delete a file
         */
        public function delete($uid) {
            if(is_numeric($uid)) {
                return $this->dao->delete($this->getTable_Files(), 'pk_i_id = '.$uid);
            }
            return false;
        }
        
        /*
         * Get downloads
         */
        public function getDownloads($uid) {
            $this->dao->select('COUNT(*) as total');
            $this->dao->from($this->getTable_Stats());
            $this->dao->where('fk_i_market_id', $uid);
            $this->dao->groupBy('fk_i_market_is');
            $result = $this->dao->get() ;
            if($result!==false) {
                $row = $result->result();
                return $row[0]['total'];
            } else {
                return 0;
            }
        }       
        
        /*
         * Get plugins
         */
        public function getPlugins($page = 0) {
            return $this->getData('PLUGIN', $page);
        }
        
        /*
         * Get themes
         */
        public function getThemes($page = 0) {
            return $this->getData('THEME', $page);
        }
        
        /*
         * Get languages
         */
        public function getLanguages($page = 0) {
            return $this->getData('LANGUAGE', $page);
        }
        
        /*
         * Get search results
         */
        public function getSearch($pattern = '', $page = 0, $type = '') {
            return array();
        }
        
        /*
         * General purpouse function
         */
        public function getData($type = 'PLUGIN', $page = 0) {
            
            if($type=='THEME') {
                $catId = 96;
            } else if($type=='LANGUAGE') {
                $catId = 98;
            } else {
                $catId = 97;
            }
            
            $start = $page*$this->pageSize;
            
            $this->dao->select();
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item_description d", "d.fk_i_item_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('f.b_enabled', 1);
            $this->dao->where('i.fk_i_category_id', $catId);
            $this->dao->groupBy('m.s_slug');
            $this->dao->orderBy('f.pk_i_id', 'DESC');
            $this->dao->limit($start, $this->pageSize);
            $result = $this->dao->get() ;
            if($result!==false) {
                $data = $result->result();
                foreach($data as $k => $v) {
                    $res = ItemResource::newInstance()->getResource($v['fk_i_item_id']);
                    if($res) {
                        $data[$k]['s_image'] = osc_base_url().$res['s_path'].$res['pk_i_id'].".".$res['s_extension'];
                        $data[$k]['s_thumbnail'] = osc_base_url().$res['s_path'].$res['pk_i_id']."_thumbnail.".$res['s_extension'];
                    } else {
                        $data[$k]['s_image'] = '';
                        $data[$k]['s_thumbnail'] = '';
                    }
                    unset($data[$k]['s_contact_email']);
                }
                return $data;
            } else {
                return array();
            }
        }
        
        /*
         * General purpouse function
         */
        public function countData($type = 'PLUGIN') {

            if($type=='THEME') {
                $catId = 96;
            } else if($type=='LANGUAGE') {
                $catId = 98;
            } else {
                $catId = 97;
            }

            $this->dao->select('m.pk_i_id');
            $this->dao->from($this->getTable()." m");
            $this->dao->join($this->getTable_Files()." f ", "f.fk_i_market_id = m.pk_i_id", "LEFT");
            $this->dao->join(DB_TABLE_PREFIX."t_item i ", "i.pk_i_id = m.fk_i_item_id", "LEFT");
            $this->dao->where('f.b_enabled', 1);
            $this->dao->where('i.fk_i_category_id', $catId);
            $this->dao->groupBy("m.pk_i_id");
            
            $result = $this->dao->get() ;

            if($result!==false) {
                return $result->numRows();
            } else {
                return 0;
            }
        }
    }

?>