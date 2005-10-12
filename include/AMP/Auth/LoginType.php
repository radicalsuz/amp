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


    function AMP_Authentication_LoginType ( &$handler ) {
        $this->init( $handler );
    }

    function init( &$handler ){
        $this->_dbcon = &$handler->dbcon;
        $this->_handler = &$handler;
    }

    function check_authen_credentials() {

        // First check for an existing authentication token.
        if (isset($_COOKIE[ $this->_cookie_name ]))
            return $this->_handler->check_cookie($_COOKIE[ $this->_cookie_name ]);

        if (isset($_REQUEST[ $this->_login_username_field ]) || isset($_SERVER['PHP_AUTH_USER']))
            return $this->_handler->check_password();

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
        include( $this->_loginScreen );
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

}
?>
