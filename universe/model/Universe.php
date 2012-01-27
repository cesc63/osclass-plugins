<?php

    /**
     * Model database for Universe tables
     * 
     * @package OSClass
     * @subpackage Model
     * @since 0.1
     */
    class Universe extends DAO
    {
        /**
         * It references to self object: Universe
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var Universe
         */
        private static $instance ;

        /**
         * It creates a new Universe object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since unknown
         * @return Universe
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Construct
         */
        function __construct()
        {
            parent::__construct() ;
            $this->setTableName('t_universe_files') ;
            $this->setPrimaryKey('pk_i_id') ;
            $this->setFields( array('pk_i_id', 'fk_i_item_id', 's_slug', 's_file', 's_version', 'e_type', 'b_enabled') ) ;
        }

        /**
         * Return table name universe files
         * 
         * @return string
         */
        public function getTable_Files()
        {
            return DB_TABLE_PREFIX . 't_universe_files' ;
        }

        /**
         * Return table name universe stats
         * 
         * @return string
         */
        public function getTable_Stats()
        {
            return DB_TABLE_PREFIX . 't_universe_stats' ;
        }

        /**
         * Import sql file
         * @param type $file 
         */
        public function import($file)
        {
            $path = osc_plugin_resource($file) ;
            $sql  = file_get_contents($path) ;

            if( !$this->dao->importSQL($sql) ) {
                throw new Exception( $this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc() ) ;
            }
        }

        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Stats()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Files()) ) ;
            $this->dao->delete(sprintf("%st_plugin_category", DB_TABLE_PREFIX), "s_plugin_name = 'universe'");
        }

        /**
         * Get files from an item
         */
        public function getFilesFromItem($item_id)
        {
            $this->dao->select() ;
            $this->dao->from( $this->getTable_Files() ) ;
            $this->dao->where( 'fk_i_item_id', $item_id ) ;
            $this->dao->orderBy( 'pk_i_id', 'DESC' ) ;
            $result = $this->dao->get() ;
            if( $result !== false ) {
                return $result->result() ;
            }

            return array() ;
        } 

        /**
         * Get files
         */
        public function getFiles()
        {
            $this->dao->select() ;
            $this->dao->from( $this->getTable_Files() ) ;
            $this->dao->orderBy( 'pk_i_id', 'DESC' ) ;
            $result = $this->dao->get() ;
            if( $result !== false ) {
                return $result->result() ;
            }

            return array() ;
        } 
        
        /**
         * Get file by slug
         */
        public function getFileBySlug($slug, $version = '')
        {
            $this->dao->select( array('s_slug', 's_version', 'e_type', 'b_enabled') ) ;
            $this->dao->from( $this->getTable_Files() ) ;
            $this->dao->where( 's_slug', $slug ) ;
            $this->dao->where( 'b_enabled', 1 ) ;
            if( $version != '' ) {
                $this->dao->where('s_version', $version) ;
            }
            $this->dao->orderBy('pk_i_id', 'DESC') ;
            $this->dao->limit(1) ;
            $result = $this->dao->get() ;

            if( $result !== false ) {
                return $result->row() ;
            }

            return array() ;
        } 
        
        /**
         * Get file by slug
         */
        public function getFilesBySlug($slug)
        {
            $this->dao->select( array('s_slug', 's_version', 'e_type', 'b_enabled') ) ;
            $this->dao->from( $this->getTable_Files() ) ;
            $this->dao->where( 's_slug', $slug ) ;
            $this->dao->where( 'b_enabled', 1 ) ;
            $this->dao->orderBy( 'pk_i_id', 'DESC' ) ;
            $result = $this->dao->get() ;

            if( $result !== false ) {
                return $result->result() ;
            }

            return array() ;
        } 
        
        /**
         * Insert new file
         */
        public function insertFile($aSet)
        {
            return $this->dao->insert( $this->getTable_Files(), $aSet) ;
        }

        /**
         * Update a file
         */
        public function updateFile($item_id, $pk_i_id, $aSet)
        {
            if( !is_numeric($item_id) || !is_numeric($pk_i_id) ) {
                return false ;
            }

            return $this->dao->update( $this->getTable_Files(), $aSet, 'pk_i_id = '.$pk_i_id.' AND fk_i_item_id = '.$item_id) ;
        }

        /**
         * Update a file
         */
        public function updateFilesFromItem($item_id, $aSet)
        {
            if( !is_numeric($item_id) ) {
                return false ;
            }

            return $this->dao->update( $this->getTable_Files(), $aSet, 'fk_i_item_id = '.$item_id) ;
        }

        /**
         * Delete a file
         */
        public function deleteFile($id)
        {
            if( !is_numeric($id) ) {
                return false ;
            }

            $this->dao->delete($this->getTable_Stats(), 'fk_i_universe_id = '.$id) ;
            return $this->dao->delete($this->getTable_Files(), 'pk_i_id = '.$id) ;
        }

        /*
         * Check if slug exists or not
         */
        public function checkSlug($slug, $item_id)
        {
            $this->dao->select() ;
            $this->dao->from( $this->getTable_Files() ) ;
            $this->dao->where( 'fk_i_item_id != ' . $item_id ) ;
            $this->dao->where( 's_slug', $slug ) ;
            $result = $this->dao->get() ;
            if( $result !== false && $result->numRows() > 0 ) {
                return false ;
            }

            return true ;
        }

        /*
         * Insert new stat about a download
         */
        public function insertStat($universe_id, $remote_host, $remote_addr)
        {
            return $this->dao->insert( $this->getTable_Stats(), array(
                'fk_i_universe_id' => $universe_id,
                's_hostname'       => $remote_host,
                's_ip'             => $remote_addr,
                'dt_date'          => date('Y-m-d H:i:s')
            ) ) ;
        }

        /*
         * Get latest files added
         */
        public function getLatest($page = 0)
        {
            $this->dao->select() ;
            $this->dao->from( $this->getTable_Files() ) ;
            $this->dao->orderBy( 'pk_i_id', 'DESC' ) ;
            $this->dao->limit( $page, 20 ) ;
            $result = $this->dao->get() ;
            if( $result !== false ) {
                return $result->result() ;
            }

            return array() ;
        }

        /*
         * Disable a file
         */
        public function disable($uid)
        {
            if( is_numeric($uid) ) {
                return $this->dao->update( $this->getTable_Files(), array('b_enabled' => 0), 'pk_i_id = ' . $uid) ;
            }

            return false ;
        }

        /*
         * Enable a file
         */
        public function enable($uid)
        {
            if( is_numeric($uid) ) {
                $res = $this->dao->update( $this->getTable_Files(), array('b_enabled' => 1), 'pk_i_id = ' . $uid) ;
            }

            return false ;
        }

        /*
         * Delete a file
         */
        public function delete($uid)
        {
            if( is_numeric($uid) ) {
                return $this->dao->delete($this->getTable_Files(), 'pk_i_id = '.$uid) ;
            }

            return false ;
        }

        /*
         * Get downloads
         */
        public function getDownloads($uid)
        {
            $this->dao->select( 'COUNT(*) as total' ) ;
            $this->dao->from( $this->getTable_Stats() ) ;
            $this->dao->where( 'fk_i_universe_id', $uid ) ;
            $this->dao->groupBy( 'fk_i_universe_id' ) ;
            $result = $this->dao->get() ;
            if( $result!== false ) {
                $row = $result->row() ;
                return $row['total'] ;
            }

            return 0 ;
        }
    }

?>