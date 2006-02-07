<?php

require_once( 'AMP/Auth/LoginType.php');

class AMP_Authentication_LoginType_Admin extends AMP_Authentication_LoginType {

    var $_cookie_name = 'AMPLoginCredentials';
    var $_loginScreenText = ' Administrative Login';

    function AMP_Authentication_LoginType_Admin( &$handler ) {
        $this->init( $handler );
    }

    function validateUser( $username, $password ){
        require_once( 'AMP/System/User/User.php');

        $user = &new AMPSystem_User( $this->_dbcon );
        $user->readUsername( $username );

        if ( $user->validatePassword( $password )) {
            $this->_handler->setPermissionLevel( $user->getPermissionGroup() );
            $this->_handler->setUserId( $user->id );
            define( 'AMP_USERMODE_ADMIN', true);
            return true;
        }
            
    }

    function getHelpLinks() {
        return sprintf( AMP_TEXT_LOGIN_HELP_ADMIN, AMP_SITE_ADMIN );
    }

    function getAuthOptions( ) {
        return false;
    }


}
?>
