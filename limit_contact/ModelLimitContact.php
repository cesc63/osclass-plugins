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
     * Model database for Voting tables
     * 
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelLimitContact extends DAO
    {
        /**
         * It references to self object: ModelLimitContact.
         * It is used as a singleton
         * 
         * @access private
         * @since 3.0
         * @var ModelVoting
         */
        private static $instance ;

        /**
         * It creates a new ModelLimitContact object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since 3.0
         * @return ModelVoting
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
            parent::__construct();
        }
        
        /**
         * Return table name voting item
         * @return string
         */
        public function getTable()
        {
            return DB_TABLE_PREFIX.'t_limit_contact';
        }
        
        /**
         * Import sql file
         * @param type $file 
         */
        public function import($file)
        {
            $path = osc_plugin_resource($file) ;
            $sql = file_get_contents($path);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( "Error importSQL::ModelLimitContact<br>".$file ) ;
            }
        }
                
        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query("DROP TABLE ".DB_TABLE_PREFIX."t_limit_contact");
        }
        
        // item related --------------------------------------------------------   
        
        public function insert($values) {
            return $this->dao->insert($this->getTable(), $values);
        }
        
        /**
         *
         * @param type $ip
         * @return type int
         */
        public function countContacts( $ip )
        {
            $date = date('Y-m-d H:i:s');
            $lastDay = strtotime(date("Y-m-d H:i:s", strtotime($date)) . " -1 day");
            // select * from table where ip = ip AND date > current - 1day
            $this->dao->select();
            $this->dao->from( $this->getTable() );
            $this->dao->where( 's_ip', $ip );
            $this->dao->where('dt_date_time > "$lastDay"');
            
            $result = $this->dao->get();
            if($result === false) {
                return 0;
            }
            return $result->numRows();
        }
        public function lastInfoByip($ip)
        {
            $this->dao->select();
            $this->dao->from( $this->getTable() );
            $this->dao->where('s_ip', $ip);
            $this->dao->orderBy('dt_date_time', 'desc');
            $this->dao->limit(1);
            
            $result = $this->dao->get();
            if($result === false) {
                return 0;
            }
            return $result->row();
        }
    }
?>