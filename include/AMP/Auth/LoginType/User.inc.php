<?php

define( 'AMP_TEXT_LOGIN_PASSWORD_NEW', 'Haven\'t logged in before?<BR><a href="%s">Create password</a>');
define( 'AMP_TEXT_LOGIN_PASSWORD_RESET', 'Forgot your password? <a href="%s">Reset password</a>');
define( 'AMP_LOGIN_LOOKUPTYPE_OTP', 'otp');
define( 'AMP_LOGIN_LOOKUPTYPE_PASSWORD', 'pass');

require_once( 'AMP/Auth/LoginType.php');

class AMP_Authentication_LoginType_User extends AMP_Authentication_LoginType {

    var $_cookie_name = 'AMPUserLoginCredentials';
    var $_loginScreenText = ' User Login';
    var $_formFields = array(
        'AMPLogin_username' => 
            array( 'label' => 'Email Address:', 'type' => 'text'),
        'AMPLogin_password' => 
            array( 'label' => 'Password:', 'type' => 'password')
            );
    var $_passwordEditUrl;
    var $_allowedActions = array( 'reset', 'create' );
    var $_lookupType = AMP_LOGIN_LOOKUPTYPE_PASSWORD;
    var $_default_permission = 0;
    var $_login_password_field_confirmed = 'AMPLogin_password_confirm';
    var $_current_action;

    function AMP_Authentication_LoginType_User( &$handler ) {
        $this->init( $handler );
        $this->_passwordEditUrl = PHP_SELF_QUERY();
    }

    function validateUser( $username, $password ){
        $user_sql = "SELECT id, Email, password FROM userdata WHERE Email=" . $this->_dbcon->qstr( $username ) . ' AND !isnull( password )'; 
        $authdata = $this->_dbcon->GetRow( $user_sql );

        if ($this->_handler->validate_password( $password, $authdata['password'] )) {
            $this->_handler->setUserId( $authdata['id'] );
            return true;
        }
            
    }

    function getNewLogin( ) {
        if ( 'create' == $this->_current_action ) return false;
        return  sprintf( AMP_TEXT_LOGIN_PASSWORD_NEW, AMP_Url_AddVars( $this->_passwordEditUrl, 'action=create')) ;

    }

    function getHelpLinks() {
        $output = "";
        if ( "reset" != $this->_current_action ) $output .= sprintf( AMP_TEXT_LOGIN_PASSWORD_RESET, AMP_Url_AddVars( $this->_passwordEditUrl, 'action=reset') ). '<BR>';
        return $output . sprintf( AMP_TEXT_LOGIN_HELP_ADMIN, AMP_SITE_ADMIN );
    }

    function getMessageCreate( ) {
        return "Create Password<BR>Step 1: Enter your email address" ;
    }

    function getMessageReset( ) {
        return "Reset Password<BR>Step 1: Enter your email address" ;
    }

    function check_authen_credentials() {
        if ( !( isset( $_REQUEST['action']) && $_REQUEST['action'])) return PARENT::check_authen_credentials( );
        
        if ( $valid_action = $this->validateAction( $_REQUEST['action'] )) {
            if ( $this->_validateOtp( $valid_action )) return $this->do_set_password( $valid_action );
            if ( $otp_active = $this->_activateOtp( $valid_action )) return $otp_active;
            return $this->_requestEmailOnly( $valid_action );
        }
        return PARENT::check_authen_credentials( );
    }

    function _requestEmailOnly( $action ) {
        unset( $this->_formFields['AMPLogin_password'] );
        $message_action = 'getMessage'.ucfirst( $action );
        if ( method_exists( $this, $message_action )) $this->_handler->set_message( $this->$message_action(), 'OK');
        return false;
    }

    function getFieldnamePassword( ) {
        if ( $this->_lookupType = AMP_LOGIN_LOOKUPTYPE_OTP ) return 'otp';
        return PARENT::getFieldnamePassword( );
    }

    function validateAction( $action ) {
        #$this->_passwordEditUrl = AMP_Url_AddVars( $this->_passwordEditUrl, 'action='.$action );
        if ( array_search( $action, $this->_allowedActions ) === FALSE ) return false;
        $this->_current_action = $action;
        return $action;
    }

    function _activateOtp( $action ){
        $emailsLookup = &AMPSystem_Lookup::instance( 'userDataEmails' );
        if ( isset( $_REQUEST[ $this->_login_username_field ])) {
            if ( !array_search( $_REQUEST[ $this->_login_username_field ], $emailsLookup )) {
                $this->_handler->set_message( 'Account Not Found', 'Error', true );
                return false;
            }
            $this->sendOtp( $_REQUEST[ $this->_login_username_field ], $_REQUEST['uid']);
            $this->_handler->set_message($this->_getMessageSentOtp( $action ), 'OK' );

            $new_form_fields['otp'] = $this->_formFields[ $this->_login_password_field ];
            $new_form_fields['otp']['label'] = 'Passcode:';
            unset( $this->_formFields );
            $this->_formFields = $new_form_fields;
            return true;
        }
        return false;
    }

    function _getMessageSentOtp ( $action ) {
        return ucfirst( $action ) . ' Password<BR>Step 2: Check your email for a validation passcode';
    }

    function _validateOtp( ) {
        $emailsLookup = &AMPSystem_Lookup::instance( 'userDataEmails' );
        if ( isset( $_REQUEST['otp']) && isset( $_REQUEST['uid'])) {
            $this->_lookupType = AMP_LOGIN_LOOKUPTYPE_OTP;
            $this->_login_username_field = 'uid';
            $fake_cookie = array( $_REQUEST['otp'], $emailsLookup[ $_REQUEST['uid']], $this->_default_permission, $_REQUEST['uid'] );
            $this->_formFields['otp'] = array( 'type' => 'hidden', 'label'=>'');
            if ( $this->_handler->check_cookie( join( ":", $fake_cookie) )) {
                $this->_passwordEditUrl = AMP_Url_AddVars( $this->_passwordEditUrl, "otp=".$_REQUEST['otp']);
                return true;
            }
        }
        return false;
    }

    function sendOtp( $email_address, $uid ) {
        $secret = $this->_handler->get_seed( );
        $hash = $this->_handler->make_secure_cookie( $email_address, $this->_default_permission, $secret );
        $this->_handler->save_session( $hash, $secret );

        require_once( 'AMP/System/Email.inc.php');
        $emailMessage = &new AMPSystem_Email( );
        $emailMessage->setRecipient( $email_address );
        $emailMessage->setSubject( AMP_SITE_NAME . $this->_loginScreenText );
        $emailMessage->setMessage( 'Login here: ' . AMP_Url_AddVars( AMP_SITE_URL . $this->_passwordEditUrl, array( 'uid=' . $uid, 'otp='. $hash )  ));
        return $emailMessage->execute( );
    }

    function do_set_password( $action ){
        print 'bananas';
        if ( !( $new_password = $this->create_confirmed_password( $action ))) return false;
        print 'yesllow';
        $sql = "UPDATE userdata set password = " . $this->_dbcon->qstr( $new_password ) . " where id = " . $_REQUEST['uid'];
        $this->_dbcon->Execute( $sql );
        $this->_handler->set_cookie( );
        $this->_handler->setUserId( $_REQUEST['uid']);
        return true;

    }

    function _getMessageCreateNew( $action ) {
        return ucfirst( $action ) . ' Password<BR>Step 3: Enter your email and your desired password';
    }

    function create_confirmed_password( $action ) {
        $this->_handler->set_message( $this->_getMessageCreateNew( $action ), 'OK' );
        if ( !( isset( $_REQUEST[ $this->_login_password_field_confirmed ]) && isset( $_REQUEST[ $this->_login_username_field ] ))) {
            $this->add_confirm_field( );
            return false;
        }

        $password_match = ( $_REQUEST[ $this->_login_password_field_confirmed ] == $_REQUEST[ $this->_login_password_field ] ) ;
        if (!$password_match ) {
            $this->_handler->set_message( 'Passwords did not match ', 'Error' );
            $this->add_confirm_field( );
            return false;
        }
        return $_REQUEST[ $this->_login_password_field_confirmed ];

    }

    function add_confirm_field ( ) {
        $this->_formFields[ $this->_login_password_field_confirmed ] = 
                array( 'label' => 'Confirm Password:', 'type'=>'password');
    }

}
?>
