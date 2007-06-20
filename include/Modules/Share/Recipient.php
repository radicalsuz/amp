<?php
require_once( 'AMP/User/Profile/Profile.php' );

class Share_Recipient extends AMP_User_Profile {

    function Share_Recipient( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

}

?>
