<?php

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

    function UserDataPlugin_Authenticate_AMPPassword ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function execute ( $options = null ) {

        $options = array_merge($this->getOptions(), $options);
        require_once( 'AMP/Auth/Handler.inc.php');
        $AMP_Auth_Handler = &new AMP_Authentication_Handler( $this->udm->dbcon, 'user' );
		$AMP_Auth_Handler->userid = $options['uid'];
        if ( !( $this->udm->authorized = $AMP_Auth_Handler->is_authenticated( ) )) $AMP_Auth_Handler->do_login( );

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
}
?>
