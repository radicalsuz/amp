<?php
require_once( 'AMP/Content/Manager.inc.php');

class AMP_System_Component_Display extends AMPContent_Manager {
    var $_controller;
    var $_renderer;

    function AMP_System_Component_Display( &$controller ){
        $this->init( $controller );
    }

    function init( &$controller ){
        $this->_controller = &$controller;
        $this->_renderer = &new AMPDisplay_HTML( );
    }

    function add( &$display, $display_key = null ){
        return $this->addDisplay( $display, $display_key );
    }

    function &instance( &$controller ) {
        static $manager = false;
        if (!$manager) $manager = new AMP_System_Component_Display( $controller );
        return $manager;
    }

    function add_nav( $nav_name ){
        // interface
    }

    function get_navs( ){
        // interface
        return false;
    }
}

?>
