<?php
require_once( 'AMP/Content/Manager.inc.php');
require_once( 'AMP/Content/Display/HTML.inc.php');

class AMP_System_Component_Display extends AMPContent_Manager {
    var $_renderer;

    function AMP_System_Component_Display( ){
        $this->__construct( );
    }

    function __construct( ){
        $this->_renderer =  AMP_get_renderer( );
    }

    function &instance( ) {
        static $manager = false;
        if (!$manager) $manager = new AMP_System_Component_Display( );
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
