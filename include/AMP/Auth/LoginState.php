<?php

class AMP_Authentication_LoginState {

    var $_formFields = array(
        'AMPLogin_username' => 
            array( 'label' => 'Email Address:', 'type' => 'text'),
        'AMPLogin_password' => 
            array( 'label' => 'Password:', 'type' => 'password')
            );
    var $_current_action = 'login';
    var $_handler;
    var $_sequentialStates = array( 'login_active' => 'AMP_Authentication_LoginState_Base' );
    var $_message_OK = true;


    function AMP_Authentication_LoginState( ){
    }

    function init(  &$handler ){
        $this->_handler = &$handler;
    }

    function &initState( ){
        if ( !( $new_state = $this->_validateState() )) {
			$this->notice('no interesting state validating, returning this: '.get_class($this));
			return $this;
		}
		$this->notice('instantiating a new valid state: '.$new_state);
        $state = &new $new_state( $this->_handler );
        $this->_passState( $state );
        return $state->initState();
    }

    function getMessageType( ) {
        if ( $this->_message_OK ) return 'OK';
        return 'Error';
    }

    function _validateState( ){
        if ( $this->_readAuthRequest( )) {
			$action = $this->_sequentialStates[ 'action_requested'];
			$this->notice('valid state is '.$action);
			return $action;
		}
		$this->error('validateState failed');
        return false;
    }


    function _passState( &$state ) {
         
    }

    function setAction( $action ) {
        $this->_current_action = $action;

    }

    function getMessage( ){
        return false;
    }

    function getHelpLinks( ){

    }

    function getFieldnamePassword( ) {
        return false;
    }

    function getAuthOptions( ) {
        return false; 
    }

    function getFormFields( ) {
        return $this->_formFields;
    }

    function getLoginUrl( $change_array=null ){
        if ( !isset( $change_array )) return PHP_SELF_QUERY();
        $vars = array_merge( AMP_Url_Values( ) , $change_array);
        return AMP_Url_AddVars( $_SERVER['PHP_SELF'] , $vars );
    }

	function error($message, $level = E_USER_WARNING) {
		if(defined('AMP_AUTHENTICATION_DEBUG') && AMP_AUTHENTICATION_DEBUG ) {
			trigger_error($message, $level);
		}
		$this->errors[] = $message;
	}

	function notice($message) {
		return $this->error($message, E_USER_NOTICE);
	}

	function isAuthenticated() {
		return false;
	}
}

class AMP_Authentication_LoginState_Base extends AMP_Authentication_LoginState {

    var $_allowedActions = array( 'reset_password', 'create_password' );
    var $_sequentialStates = array( 'action_requested' => 'AMP_Authentication_LoginState_AuthRequest',
                                    'login_failed' => 'AMP_Authentication_LoginState_InvalidLogin');

    function AMP_Authentication_LoginState_Base ( &$handler ) {
        $this->init( $handler );
    }


    function _readAuthRequest( ) {
        if ( !( isset( $_REQUEST['action']) && $_REQUEST['action'])) {
			$this->error('in LoginState_Base::_readAuthRequest, no action requested');
			return false;
		}
        if ( array_search( $_REQUEST['action'], $this->_allowedActions ) === FALSE ) {
			$this->error('in LoginState_Base::_readAuthRequest, action not allowed');
			return false;
		}
		$this->notice('returning current action as '.$_REQUEST['action']);
        return ( $this->_current_action = $_REQUEST['action'] );
    }

    function getHelpLinks() {
        return  sprintf( AMP_TEXT_LOGIN_PASSWORD_RESET, $this->getLoginUrl( array( 'action' => 'action=reset_password' ))). '<BR>'
              . sprintf( AMP_TEXT_LOGIN_HELP_ADMIN, AMP_SITE_ADMIN );
    }

    function getAuthOptions() {
        return  sprintf( AMP_TEXT_LOGIN_PASSWORD_NEW, $this->getLoginUrl(  array( 'action' =>'action=create_password' ))) ;
    }

}

class AMP_Authentication_LoginState_AuthRequest extends AMP_Authentication_LoginState {
    var $_sequentialStates = array( 'action_requested' => 'AMP_Authentication_LoginState_OtpConfirm' );
    var $_login_username_field = 'AMPLogin_username' ;
    var $_default_permission = 0;
    var $_login_otp_field = 'otp';
    var $_formFields = array(
        'AMPLogin_username' => 
            array( 'label' => 'Email Address:', 'type' => 'text'));

    function AMP_Authentication_LoginState_AuthRequest( &$handler ) {
        $this->init( $handler );
    }

    function _readAuthRequest( ){
        if ( isset( $_REQUEST[ $this->_login_otp_field]) && $_REQUEST[ $this->_login_otp_field ]) {
			$this->notice('in LoginState_AuthRequest::_readAuthRequest, otp field is set');
			return true;
		}
        if ( !( isset( $_REQUEST[ $this->_login_username_field]) && $_REQUEST[ $this->_login_username_field ]) ) {
			$this->error('in LoginState_AuthRequest::_readAuthRequest, no otp or username');
			return false;
		}
        $emailsLookup = &AMPSystem_Lookup::instance( 'UserDataEmails' );
		$uids = array_keys($emailsLookup, $_REQUEST[ $this->_login_username_field ]);
        if ( !array_search( $_REQUEST['uid'], $uids)) {
			$this->notice('username field not found in userdata');
            $this->_message_OK = false;
            return false;
        }
        $this->sendOtp( $_REQUEST[ $this->_login_username_field ], $_REQUEST['uid']);
        return true;
    }

    function sendOtp( $email_address, $uid ) {
        $secret = $this->_handler->get_seed( );
        $hash = $this->_handler->make_secure_cookie( $email_address, $this->_default_permission, $secret );
        $this->_handler->save_session( $hash, $secret );

        require_once( 'AMP/System/Email.inc.php');
        $emailMessage = &new AMPSystem_Email( );
        $emailMessage->setRecipient( $email_address );
        $emailMessage->setSubject( AMP_SITE_NAME . ' Password ' . ucfirst( $this->_current_action) );
        $emailMessage->setMessage( 'Passcode: '.$hash.'\n\n or login here: ' .  AMP_SITE_URL . substr( $this->getLoginUrl(array( 'uid=' . $uid, 'otp='. $hash )  ), 1 )); 
        return $emailMessage->execute( );
    }
    
    function getHelpLinks( ){
        $output = "";
        if ( "reset_password" != $this->_current_action ) $output .= sprintf( AMP_TEXT_LOGIN_PASSWORD_RESET,  $this->getLoginUrl( array( 'action' => 'action=reset_password') )). '<BR>';
        return $output . sprintf( AMP_TEXT_LOGIN_HELP_ADMIN, AMP_SITE_ADMIN );
    }

    function getAuthOptions() {
        if ( 'create_password' != $this->_current_action ) return false;
        return  sprintf( AMP_TEXT_LOGIN_PASSWORD_NEW, $this->getLoginUrl( array(  'action'=>'action=create_password') )) ;
    }

    function getMessage( ) {
        if ( $this->_message_OK ) return ucfirst( $this->_current_action ) . " Password<BR>Step 1: Enter your email address" ;
        return 'Account Not Found' ;
    }

}

class AMP_Authentication_LoginState_OtpConfirm extends AMP_Authentication_LoginState {
    var $_formFields = array(
        'otp' =>  array( 'label' => 'Passcode:', 'type' => 'password' ));
    var $_login_otp_field = 'otp';
    var $_default_permission = 0;
    var $_sequentialStates = array( 'action_requested' => 'AMP_Authentication_LoginState_SetPassword');
    var $_validated = false;

    function AMP_Authentication_LoginState_OtpConfirm( &$handler ) {
        $this->init( $handler );
    }

    
    function getMessage( ){
        return ucfirst( $this->_current_action ) . ' Password<BR>Step 2: Check your email for a validation passcode';
    }

    function _readAuthRequest( ){
        if ( !( isset( $_REQUEST[ $this->_login_otp_field ]) && $_REQUEST[ $this->_login_otp_field ]) ) {
			$this->error('in LoginState_OtpConfirm::_readAuthRequest, otp field not set');
			return false;
		}
        return $this->_validateOtp( );
    }

    function _validateOtp( ) {
        $emailsLookup = &AMPSystem_Lookup::instance( 'userDataEmails' );
        if ( isset( $_REQUEST['otp']) && isset( $_REQUEST['uid'])) {
            $fake_cookie = array( $_REQUEST['otp'], $emailsLookup[ $_REQUEST['uid']], $this->_default_permission, $_REQUEST['uid'] );
            $this->_formFields['otp'] = array( 'type' => 'hidden', 'label'=>'');
            if ( $this->_handler->check_cookie( join( ":", $fake_cookie) )) {
				$this->notice('fake cookie checks out');
                
                #$this->_passwordEditUrl = AMP_Url_AddVars( $this->_passwordEditUrl, "otp=".$_REQUEST['otp']);
                return $this->_validated = true;
            }
			$this->error('fake cookie not valid');
        }
		$this->error('uid and otp not set');
        return false;
    }

    function getAuthOptions() {
        return false;
    }

    function getLoginUrl( ){
        if ( $this->_validated ) return PARENT::getLoginUrl( array( $this->_login_otp_field => $_REQUEST[ $this->_login_otp_field]));
        return PARENT::getLoginUrl( );
    }
}
class AMP_Authentication_LoginState_SetPassword extends AMP_Authentication_LoginState {
    var $_formFields = array(
        'AMPLogin_password' => 
            array( 'label' => 'Password:', 'type' => 'password'),
        'AMPLogin_password_confirm' =>
            array( 'label' => 'Confirm Password:', 'type'=>'password')
            );

    var $_login_password_field = 'AMPLogin_password';
    var $_login_password_field_confirmed = 'AMPLogin_password_confirm';
    var $_login_otp_field = 'otp';

	var $_authenticated = false;

    function AMP_Authentication_LoginState_SetPassword( &$handler ){
        $this->init( $handler );
    }

    function _validateState( ) {
        if($this->_readAuthRequest( )) {
			$this->_authenticated = true;
		}
		$this->notice('in validate state, returning false, just cause');
        return false;
    }

	function isAuthenticated() {
		return $this->_authenticated;
	}

    function _readAuthRequest() {
        if ( !( isset( $_REQUEST[ $this->_login_password_field_confirmed ]) && $_REQUEST[ $this->_login_password_field_confirmed ])) return false;

        $password_match = ( $_REQUEST[ $this->_login_password_field_confirmed ] == $_REQUEST[ $this->_login_password_field ] ) ;
        if ( $password_match ) {
			$setpass = $this->setPassword( $_REQUEST[ $this->_login_password_field_confirmed ]);
			$this->notice('readAuthRequest returning results of setPassword: '.$setpass);
			return $setpass;
		} else {
			$this->error('passwords do not match');
		}

        return ( $this->_message_OK = false );
        
    }

    function getMessage() {
        if ( $this->_message_OK ) return ucfirst( $this->_current_action ) . ' Password<BR>Step 3: Enter and confirm your desired password';
        return 'Passwords did not match';
    }

    function setPassword( $new_password ){
        #$sql = "UPDATE userdata set password = " . $this->_dbcon->qstr( $new_password ) . " where id = " . $_REQUEST['uid'];
        $sql = "UPDATE userdata set password = " . $this->_handler->dbcon->qstr( $new_password ) . " where Email = " . $this->_handler->dbcon->qstr( $this->_lookupEmail( $_REQUEST['uid'] ));
        if ( !$this->_handler->dbcon->Execute( $sql )) {
			$this->error('could not execute '.$sql);
			return false;
		} else {
			$this->notice('did update - '.$sql);
		}
        $this->_handler->set_cookie( );
        $this->_handler->setUserId( $_REQUEST['uid']);
        return true;
    }

    function _lookupEmail( $uid ){
        static $emailsLookup=false;
        if ( !$emailsLookup ) $emailsLookup = &AMPSystem_Lookup::instance( 'userDataEmails');
        if ( !isset( $emailsLookup[ $uid ])) {
			$this->error('no email for uid: '.$uid);
			return false;
		}
        return $emailsLookup[ $uid ];
    }
    function getLoginUrl( ){
        return PARENT::getLoginUrl( array( $this->_login_otp_field => $this->_login_otp_field . '=' . $_REQUEST[ $this->_login_otp_field]));
    }
}

?>
