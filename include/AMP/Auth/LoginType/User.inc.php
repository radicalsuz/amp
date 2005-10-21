<?php

define( 'AMP_TEXT_LOGIN_PASSWORD_NEW', 'Haven\'t logged in before?<BR><a href="%s">Create password</a>');
define( 'AMP_TEXT_LOGIN_PASSWORD_RESET', 'Forgot your password? <a href="%s">Reset password</a>');
define( 'AMP_LOGIN_LOOKUPTYPE_OTP', 'otp');
define( 'AMP_LOGIN_LOOKUPTYPE_PASSWORD', 'pass');

require_once( 'AMP/Auth/LoginType.php');
require_once( 'AMP/Auth/LoginState.php');

class AMP_Authentication_LoginType_User extends AMP_Authentication_LoginType {

    var $_cookie_name = 'AMPUserLoginCredentials';
    var $_loginScreenText = ' User Login';
    var $_default_permission = 0;
    var $_current_action;
    var $_state;
    var $_invalid_message=false;

    function AMP_Authentication_LoginType_User( &$handler ) {
        $this->init( $handler );
    }

    function init( &$handler ) {
        PARENT::init( $handler );
		if(!defined('AMP_AUTHENTICATION_DEBUG')) {
			define('AMP_AUTHENTICATION_DEBUG', false);
		}
    }

    function initLoginState( ){
        $state = &new AMP_Authentication_LoginState_Base( $this->_handler );
        $this->_state = &$state->initState( );
        if ( $message = $this->_state->getMessage( )) {
            $this->_handler->set_message( $message, $this->_state->getMessageType( ));
        }
    }

    function validateUser( $username, $password ){
        $user_sql = "SELECT id, Email, password FROM userdata WHERE Email=" . $this->_dbcon->qstr( $username ) . ' AND !(isnull( password ) OR password=\'\')'; 
		if($this->_handler->userid) {
			$user_sql .= ' AND id='.$this->_handler->userid;
		}
        $authdata = $this->_dbcon->GetRow( $user_sql );
		if(!$authdata) {
			$this->error('no rows returned with login email and not null password');
		}

		$this->notice('validating password '.$password.' against '.$authdata['password']);
        if ($this->_handler->validate_password( $password, $authdata['password'] )) {
            $this->_handler->setUserId( $authdata['id'] );
            return true;
        }

		$this->error('user not validated');
            
    }

    function getAuthOptions( ) {
        return $this->_state->getAuthOptions( );
    }

    function getHelpLinks() {
        return $this->_state->getHelpLinks( );
    }

    function getFormFields( ) {
        $this->_formFields = $this->_state->getFormFields( );
        return PARENT::getFormFields( );
    }

    function getMessage( ) {
        return $this->_state->getMessage( );
    }

    function getLoginUrl( ){
        if ( $url = $this->_state->getLoginUrl( )) return $url;
        return PARENT::getLoginUrl( );
    }



    function getFieldnamePassword( ) {
        if ( $pname = $this->_state->getFieldnamePassword( )) return $pname;
        return PARENT::getFieldnamePassword( );
    }

    function check_authen_credentials( ) {
		$this->notice('in user::check_authen_credentials');
        $valid = PARENT::check_authen_credentials( ) || $this->_state->isAuthenticated();
        if ( $valid ) {
			if( (isset($_REQUEST['uid']) && $_REQUEST['uid'])
				&& $_REQUEST['uid'] != $this->_handler->userid ) {
				$this->error('auth creds do not match request : '
					.'request - '.$_REQUEST['uid'].', handler - '.$this->_handler->userid);
				return false;
			}
			$this->notice('parent auth check returned true');
        }
		$this->error('base authen credential check failed');
        return $valid;
    }

/*
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
        if ( !( $new_password = $this->create_confirmed_password( $action ))) return false;
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
*/
}
?>
