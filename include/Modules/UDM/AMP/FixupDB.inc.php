<?php

class UserDataPlugin_FixupDB_AMP extends UserDataPlugin {

    var $short_name  = 'FixupDB';
    var $long_name   = 'Database Fixup';
    var $description = 'Helper to silently upgrade databases';

    // This plugin is not available for external use.
    var $available = false;

    function UserDataPlugin_FixupDB_AMP ( &$udm, $plugin_instance=null ) {
        $this->init(&$udm, $plugin_instance);
    }

    function execute ( $options = null ) {

        $dbcon =& $this->udm->dbcon;

        // Fetch DB structure.
        $fields = $dbcon->MetaColumnNames('userdata_fields');

        // Add 'publish' flag
        if (!array_search( 'publish', $fields )) {
            $sql = 'ALTER TABLE userdata_fields ADD COLUMN publish INT(1) DEFAULT NULL';
            $dbcon->Execute($sql) or
                die( "Couldn't fixup database structure: " . $dbcon->ErrorMsg() );
        }

        // More fixups here:
        // nothing yet.

    }

}

?>
