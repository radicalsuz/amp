<?php

class AMP_Authentication_LoginType {

    var $_dbcon;

    var $_loginScreen = 'AMP/Auth/LoginScreen.inc.php';
    var $_login_username_field = "AMPLogin_username";
    var $_login_password_field = "AMPLogin_password";

    var $_cookie_name;
    var $_timeout = 43200;					// default timeout of 12 hours.

    var $_loginScreenText;

    var $_fieldTemplate = "<label for=\"%1\$s\">%2\$s</label> <input type=\"%3\$s\" name=\"%1\$s\" />";
    var $_formFields = array(
        'AMPLogin_username' => 
            array( 'label' => 'Username:', 'type' => 'text'),
        'AMPLogin_password' => 
            array( 'label' => 'Password:', 'type' => 'password')
            );
    var $_invalid_message = 'Invalid Password';

	var $errors;

    function AMP_Authentication_LoginType ( &$handler ) {
        $this->init( $handler );
    }

    function init( &$handler ){
        $this->_dbcon = &$handler->dbcon;
        $this->_handler = &$handler;
    }

    function check_authen_credentials() {

        // First check for an existing authentication token.
        if (isset($_COOKIE[ $this->_cookie_name ])) {
			$this->notice('cookie exists');
            if( $result = $this->_handler->check_cookie($_COOKIE[ $this->_cookie_name ])) return $result;
		}

        //Check for a temporary auth token
        if (isset($_GET[ $this->_cookie_name ]) && isset( $_GET['authtype']) && $_GET['authtype'] == 'temp') {
            if( $result = $this->_handler->check_cookie($_GET[ $this->_cookie_name ])) {
                $cookie_bits = explode( ':', $_GET[$this->_cookie_name ]);
                $hash = $cookie_bits[0];
                $this->_handler->set_authen_tokens( );
                $this->_dbcon->Execute( 'DELETE FROM users_sessions where hash=' . $this->_dbcon->qstr( $hash ));

                return $result;
            }
		}


        if (isset($_REQUEST[ $this->_login_username_field ]) || isset($_SERVER['PHP_AUTH_USER'])) {
			$this->notice('checking password');
            return $this->_handler->check_password();
		}

		$this->error('neither cookie nor password found');
        return false;

    }

    function submittedUser( ) {
        if (!isset($_REQUEST[ $this->_login_username_field ])) return false;
        return $_REQUEST[ $this->_login_username_field ];
    }

    function submittedPassword( ) {
        if (!isset($_REQUEST[ $this->_login_password_field ])) return false;
        return $_REQUEST[ $this->_login_password_field ];
    }

    function validateUser( $username, $password ){
        // abstract
        trigger_error( 'No login type defined');
    }

    function showLoginScreen( ) {
        $login_manager = $this;
        include( $this->_loginScreen );
    }

    function getLoginUrl( ){
        return PHP_SELF_QUERY( );
    }

    function getCookieName( ){
        return $this->_cookie_name;
    }

    function getTimeout( ){
        return $this->_timeout;
    }

    function setTimeout( $timeout_seconds ) {
        if ( !is_int( $timeout_seconds)) return false;
        $this->_timeout = $timeout_seconds;
    }

    function getHelpLinks() {
        return sprintf( AMP_TEXT_LOGIN_HELP_ADMIN, AMP_SITE_ADMIN );
    }

    function getFormFields( ){
        $output = "";
        foreach( $this->_formFields as $fieldkey => $formField) {
            $output .= sprintf( $this->_fieldTemplate, $fieldkey, $formField['label'], $formField['type']);
        }
        return $output;
    }
    function getFieldnamePassword( ) {
        return $this->_login_password_field;
    }
    function getAuthFieldnames( ) {
        return array( $this->_login_username_field, $this->_login_password_field);
    }
    function clearAuthFields( ) {
        $postFields = $this->getAuthFieldnames( );
        foreach( $postFields as $authfield ) {
            unset( $_POST[ $authfield ]);
        }
    }
    function getNewLogin( ){
        return false;
    }

    function getInvalidMessage( ){
        return $this->_invalid_message;
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

    function getAuthOptions( ) {
        return false;
    }
}
?>
