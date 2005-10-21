<?php

require_once('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Authenticate_AMPPassword extends UserDataPlugin {

    var $short_name  = 'AMPAUTHPW';
    var $long_name   = 'Password Authentication Module';
    var $description = 'Authenticates users, enabling them to update their data';

    var $options = array( 'validity' => array( 'description' => 'Session Validity (seconds)',
                                               'label'       => 'Session Validity (seconds)',
                                               'type'        => 'text',
                                               'size'        => '5',
                                               'default'     => '7200',
                                               'available'   => true ),
                           'uid' =>  array( 'label' => 'User ID',
                                            'default'     => null,
                                            'available'   => false ),
                           'pass' => array( 'label' => 'Password',
                                            'available' => false ) 
                           
                           );

    var $available = true;

	var $_handler = null;

    function UserDataPlugin_Authenticate_AMPPassword ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

	function init(&$udm, $plugin_instance=null) {
		parent::init($udm, $plugin_instance);
		if(!defined('AMP_AUTHENTICATION_DEBUG')) {
			define('AMP_AUTHENTICATION_DEBUG', false);
		}
	}

    function execute ( $options = null ) {

        $options = array_merge($this->getOptions(), $options);
        require_once( 'AMP/Auth/Handler.inc.php');
        $AMP_Auth_Handler = &new AMP_Authentication_Handler( $this->udm->dbcon, 'user' );
		$this->notice('just created auth handler');
		$this->_handler =& $AMP_Auth_Handler;
		$this->notice('just set handler');
		$AMP_Auth_Handler->userid = $options['uid'];
		$authenticated = $AMP_Auth_Handler->is_authenticated();
		$this->notice('just checked is_authenticated');
        if ( !$authenticated ) {
			$this->notice('not authenticated, doing login');
			$AMP_Auth_Handler->do_login( );
		}
		$this->udm->authorized = $authenticated;
		$this->notice('we are authenticated');

/*
        $authStatus = false;

        $uid  = $options[ 'uid' ];
        $pass = $options[ 'pass' ];
        $dbcon =& $this->udm->dbcon;

        if ( $uid && $pass ) {

            $encrypted_pass = sha1( $pass );
            $sql = "SELECT id, password from userdata WHERE".
            $sql .= " id=". $dbcon->qstr( $uid );
            $sql .= " AND password=". $dbcon->qstr( $encrypted_pass );

            $rs = $dbcon->CacheExecute( $sql ) or die( "Couldn't obtain login information: " . $dbcon->ErrorMsg() );
        
            if ( !$rs ) return $this->_failAuth( );

        } elseif ( $uid ) {
            if ( !$this->readAuthCookie( )) return $this->_failAuth( );
        }

        $authStatus = true;
        $this->udm->authorized = true;
        */
		$this->notice('setting udm->uid to auth handlers - '.$AMP_Auth_Handler->userid);
        $this->udm->uid = $AMP_Auth_Handler->userid;
        return $this->udm->uid;
        /*
         * $this->udm->pass = $pass;
        $this->_setAuthCookie( $uid, $pass );
        */
    }
            

    function _failAuth() {
        $this->udm->authorized = false;
        return false;
    }

    function _setAuthCookie( $uid ) {

        $host_sha_seed = (isset($_SERVER['REMOTE_HOST'])) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR'];
        $otp = sha1( mt_rand() . $host_sha_seed . $uid . mt_rand() );

        // Insert the temporary (not really a OTP) password into the database.
        // Validity is set by SQL column DEFAULTS.
        $sql  = "INSERT INTO userdata_auth ( uid, otp ) VALUES ( ";
        $sql .= join( ", ", array( $dbcon->qstr( $uid ), $dbcon->qstr( $otp ) ) ) . " )";

    }

    function error($message, $level = null) {
        if(defined('AMP_AUTHENTICATION_DEBUG') && AMP_AUTHENTICATION_DEBUG) {
            trigger_error($message, $level);
        }
        $this->errors[] = $message;
    }   
        
    function notice($message) {
        $this->error($message, E_USER_NOTICE);
    }
}
?>
