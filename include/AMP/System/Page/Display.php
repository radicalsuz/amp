<?php

require_once( 'AMP/System/BaseTemplate.php');
require_once( 'AMP/System/Component/Display.inc.php');

class AMP_System_Page_Display extends AMP_System_Component_Display {
    
    var $_template_class = 'AMPSystem_BaseTemplate';
    var $_template_active = true;
    var $_template;

    var $_nav_set = array( );

    function AMP_System_Page_Display ( ){
        $this->__construct( );
    }


    function execute( ){
        $output = parent::execute( );
        return $this->template( $output );
    }

    function &instance( ){
        static $page_display = false;
        if ( !$page_display ) $page_display = new AMP_System_Page_Display( );
        return $page_display;
    }

    function template( $content ){
        if ( !$template = &$this->get_template( )) return $content;
        return $template->execute( $content );

    }

    function &get_template( ){
        $empty_value = false;
        if ( !$this->_template_active ) return $empty_value;
        if ( isset( $this->_template )) return $this->_template;
        
        $template = call_user_func( array( $this->_template_class, 'instance'));
        if ( isset( $GLOBALS['modid'])) $template->setTool( $GLOBALS['modid']);
        $template->setToolName( $this->get_navs( ));
        $this->_template = &$template;
        return $this->_template;
    }

    function set_template( $template_class ) {
        if ( !$template_class ) {
            $this->_template_active = false;
            return;
        }
        $this->_template_class = $template_class;
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
