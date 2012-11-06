<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.') ;

class Osclasscom
{
    var $dao_com;

    public function __construct()
    {
        $m_db = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->dao_com = new DBCommandClass($m_db);
        osc_add_hook('init_admin', array(&$this, 'login_admin_datetime'));
    }

    public function login_admin_datetime()
    {
        if( !osc_is_admin_user_logged_in() ) {
            return false;
        }

        $this->dao_com->set('dt_last_login', date('Y-m-d H:i:s'));
        $this->dao_com->where('s_site', getSiteInfo('s_site', 'empty'));
        $this->dao_com->update('tbl_sites');
    }
}
$osclasscom = new Osclasscom();

// End of file: ./osclasscom_common/class/osclasscom.class.php