<?php

class AMPSystem_ComponentRequest {

    var $_controller;
    var $_request_vars;

    function AMPSystem_ComponentRequest( &$controller ){
        $this->_controller = &$controller;
        $this->_request_vars = AMP_Url_Read( );
    }

    function execute( ){
        if ( $this->assertAction( 'list')) {
            $this->_controller->showList( true );
            $this->_controller->setCompletedAction( 'list' );
            return true;
        }
        if ( $this->assertAction( 'inline_update')) {
            $this->_controller->doAction( 'InlineUpdate' );
            return true;
        }
        return false;
    }

    function assertAction( $action ){
        return $this->assertVar( 'action', $action );
    }

    function assertVar( $varname, $value ){
        return ( isset( $this->_request_vars[ $varname ]) && $this->_request_vars[ $varname ] == $value );
    }

}

?>
