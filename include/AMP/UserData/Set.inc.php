<?php

require_once( 'AMP/UserData.php' );

class UserDataSet extends UserData {

    function UserDataSet( &$dbcon, $instance, $admin = false ) {

        $this->UserData( $dbcon, $instance, $admin );

    }

}

?>
