<?php

require_once( 'AMP/System/BaseTemplate.php');
require_once( 'AMP/System/Component/Display.inc.php');

class AMP_System_Page_Display extends AMP_System_Component_Display {
    
    var $_template_class = 'AMPSystem_BaseTemplate';
    var $_template_active = true;
    var $_template;

    var $_nav_set = array( );

    function AMP_System_Page_Display ( &$controller ){
        $this->init( $controller );
    }

    function execute( ){
        $output = PARENT::execute( );
        return $this->template( $output );
    }

    function &instance( &$controller ){
        static $page_display = false;
        if ( !$page_display ) $page_display = new AMP_System_Page_Display( $controller );
        return $page_display;
    }

    function template( $content ){
        if ( !$template = &$this->get_template( )) return $content;
        return $template->execute( $content );

    }

    function &get_template( ){
        if ( !$this->_template_active ) return false;
        if ( isset( $this->_template )) return $this->_template;
        
        $template = & call_user_func( array( $this->_template_class, 'instance'));
        if ( isset( $GLOBALS['modid'])) $template->setTool( $GLOBALS['modid']);
        $template->setToolName( $this->get_navs( ));
        $this->_template = &$template;
        return $this->_template;
    }

    function add_nav( $nav_name, $nav_key = null ) {
        if ( !isset( $nav_key )) return $this->_nav_set[] = $nav_name;
        $this->_nav_set[ $nav_key ] = $nav_name ;
    }

    function get_navs( ){
        if ( count( $this->_nav_set ) == 0 ) return false;
        return $this->_nav_set;
    }
}

?>