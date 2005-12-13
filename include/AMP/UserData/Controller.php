<?php

class UserData_Controller {

    var $_udm;
    var $_otp;
    var $_auth;

    var $_uid;
    var $_sub;

    function UserData_Controller( &$udm ) {
        $this->init( $udm );
    }

    function init( &$udm ) {
        $this->_udm = &$udm;
        $this->_readRequest();
        $this->checkIdentity( );
    }

    function checkIdentity() {
        if ( !$this->_uid = $this->_udm->findDuplicates( )) return true;
        $this->_auth = $this->_udm->authenticate( $this->_uid, $this->_otp );
        return $this->_auth;
    }

    function _readRequest() {
        $this->_uid = ( isset( $_REQUEST['uid'] )) ? $_REQUEST['uid'] : false;
        $this->_otp = ( isset( $_REQUEST['otp'] )) ? $_REQUEST['otp'] : null;

        $this->_sub = isset($_REQUEST['btnUdmSubmit']) && $this->_udm->formNotBlank();
    }

    function isSubmitted() {
        return $this->_sub;
    }

    function isAuthorized( ){
        return $this->_auth;
    }

    function getUserId( ){
        return $this->_uid;
    }

    function execute( ){
        $this->doAction( );
    }

    function &getDisplay( ){
        if ( isset( $this->_display )) return $this->_display( );
        require_once( 'AMP/Content/Display/UserData.php');
        $this->_display = &new AMPDisplay_UserData( $this->_udm );
        return $this->_display;
    }

    function display( ){
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function doAction( ){
        if (( !$this->getUserId() || $this->isAuthorized( )) && $this->isSubmitted( )) {
            return $this->doSave( );
        }

        if ( $this->getUserId() && $this->isAuthorized() && !$this->isSubmitted( ) ){
            return $this->doRead( );
        }

        return $this->doNew( );
    }

    function doSave( ){
        $this->_udm->saveUser( );
    }

    function doRead( ){
        $this->_udm->submitted = false;
        $this->_udm->getUser( $uid ); 
    }

    function doNew( ){
        //interface
    }

}

?>
