<?php

require_once('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_DuplicateCheck_AMP extends UserDataPlugin {

    var $short_name  = 'AMPDupCheck';
    var $long_name   = 'Duplicate Record Check';
    var $description = 'Checks for duplicate records in the AMP database before saving.';

    var $available = true;

    function UserDataPlugin_DuplicateCheck_AMP ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function execute ( $options = null ) {

        $dbcon =& $this->udm->dbcon;

        if ( !isset( $_REQUEST['Email'] ) || (strlen( $_REQUEST['Email'] ) < 5) )  {
            return false;
        } else {
            $email = $_REQUEST['Email'];
        }

        $sql = "SELECT id FROM userdata WHERE " .
               " Email=" . $dbcon->qstr( $email ) .
               " AND modin=" . $dbcon->qstr( $this->udm->instance );

        $rs = $dbcon->CacheExecute( $sql );

        if ( $rs ) {
            $row = $rs->FetchRow();
            return $row['id'];
        } else {
            return false;
        }

    }
}

?>
