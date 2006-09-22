<?php
require_once( 'AMP/Content/Manager.inc.php');
require_once( 'AMP/Content/Display/HTML.inc.php');

class AMP_System_Component_Display extends AMPContent_Manager {
    var $_controller;
    var $_renderer;

    function AMP_System_Component_Display( &$controller ){
        $this->__construct( $controller );
    }

    function __construct( &$controller ){
        $this->_controller = &$controller;
        $this->_renderer =  AMP_get_renderer( );
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
