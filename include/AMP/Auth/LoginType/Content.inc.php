<?php
if ( !defined( 'AMP_FORM_ID_CONTENT_LOGIN')) define( 'AMP_FORM_ID_CONTENT_LOGIN', 20 );
require_once( 'AMP/Auth/LoginType.php');

class AMP_Authentication_LoginType_Content extends AMP_Authentication_LoginType {
    var $_cookie_name = "AMPContentLoginCredentials";
    var $_loginScreenText = ' User Login';

    function AMP_Authentication_LoginType_Content ( &$handler ){
        $this->init( $handler );
    }

    function validateUser( $username, $password ){
        $user_sql = "SELECT id, custom1 as username, custom2 as password, custom3 as permission FROM userdata WHERE modin = ". AMP_FORM_ID_CONTENT_LOGIN." and publish=1 and custom1=" . $this->_dbcon->qstr( $username ) . " ORDER BY custom3 DESC";
        $authdata = $this->_dbcon->GetRow( $user_sql );

        if ($this->_handler->validate_password( $password, $authdata['password'] )) {
            $this->_handler->setPermissionLevel( $authdata['permission'] );
            $this->_handler->setUserId( $authdata['id'] );
            return true;
        }
    }        

    function getHelpLinks( ) {
        return '&nbsp;';
    }

}
?>
