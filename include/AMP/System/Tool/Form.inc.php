<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Tool/ComponentMap.inc.php');

class Tool_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function Tool_Form( ) {
        $name = 'modules';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
    /*    
    function _formFooter( ){
        if ( !( $modid = $this->getIdValue( ) )) return false;
        
        require_once( 'AMP/System/IntroText/List.inc.php');
        require_once( 'AMP/Content/Nav/List.inc.php');
        require_once( 'AMP/System/Tool/Control/List.inc.php');
        require_once( 'AMP/System/Page/Display.inc.php');

        $intro_list = &new AMPSystem_IntroText_List( $dbcon ) ;

        $nav_list = &new Nav_List( $dbcon ) ;

        $controls_list = &new ToolControl_List( $dbcon );
        $list_array = array( 
            'Public Pages'  =>  'AMPSystem_IntroText_List',
            'Tool Settings' =>  'ToolControl_List',
            'Navigation Files'  => 'Nav_List' );
        $linked_lists = "";
        foreach ( $list_array as $header => $list_class ) {
            $linked_lists .= $this->_outputLinkedList( $list_class, $header );
        }
        return $linked_lists;
    }
    */

    function _outputLinkedList( $list_class, $header ){
        $dbcon = &AMP_Registry::getDbcon( );
        $list = &new $list_class( $dbcon ) ;
        $list->addCriteria( 'modid='.$modid);
        return AMPSystem_Page_Display::makeHeader( $header )
                . $list->output( ) ;

    }
    
}
?>
