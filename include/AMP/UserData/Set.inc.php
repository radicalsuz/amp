<?php

require_once( 'AMP/UserData.php' );

class UserDataSet extends UserData {

    function UserDataSet( &$dbcon, $instance, $admin = false ) {

        $this->UserData( $dbcon, $instance, $admin );

    }

    function _register_default_plugins () {

        // No plugins were attached to this module, but we can't very well
        // get along without data access functions. Register the default
        // AMP plugins.

        $r = $this->registerPlugin( 'Output', 'userlist_html' ) or $r;

    }





}




?>
