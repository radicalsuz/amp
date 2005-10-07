<?php

define( 'AMP_TEXT_LOGIN_HELP_ADMIN', 'If you are having trouble logging in, please contact the <a href="mailto:%s">site administrator</a>.' );
require_once( 'AMP/Auth/LoginType.php');

class AMP_Authentication_LoginType_Admin extends AMP_Authentication_LoginType {

    var $_cookie_name = 'AMPLoginCredentials';
    var $_loginScreenText = ' Administrative Login';

    function AMP_Authentication_LoginType_Admin( &$handler ) {
        $this->init( $handler );
    }

    function validateUser( $username, $password ){
        $user_sql = "SELECT id, name, password, permission FROM users WHERE name=" . $this->_dbcon->qstr( $username ) . " ORDER BY permission DESC";
        $authdata = $this->_dbcon->GetRow( $user_sql );

        if ($this->_handler->validate_password( $password, $authdata['password'] )) {
            $this->_handler->setPermissionLevel( $authdata['permission'] );
            $this->_handler->setUserId( $authdata['id'] );
            return true;
        }
            
    }

    function getHelpLinks() {
        return sprintf( AMP_TEXT_LOGIN_HELP_ADMIN, AMP_SITE_ADMIN );
    }

}
?>
