<?php

class AMP_System_Component_Display {
    var $_controller;
    var $_template_class = 'AMPSystem_BaseTemplate';
    var $_renderer;

    function AMP_System_Component_Display( &$controller ){
        $this->init( $controller );
    }

    function init( &$controller ){
        $this->_controller = &$controller;
        $this->_renderer = &new AMPDisplay_HTML( );
    }

}

?>
