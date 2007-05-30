<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Tool/ComponentMap.inc.php');

class Tool_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';
    var $_linked_pages = array( 
        AMP_MODULE_ID_CONTACT_US => AMP_CONTENT_URL_CONTACT_US, 
        AMP_MODULE_ID_TELL_A_FRIEND => AMP_CONTENT_URL_TELL_A_FRIEND, 
        );

    function Tool_Form( ) {
        $name = 'modules';
        $this->init( $name );
    }

    function adjustFields( $fields ) {
        $tool_id = ( isset( $_REQUEST['id']) && $_REQUEST['id']) ? $_REQUEST['id'] : false;
        if ( !$tool_id || !isset( $this->_linked_pages[$tool_id])) return $fields;
        $url = AMP_SITE_URL . $this->_linked_pages[ $tool_id ];
        $renderer = AMP_get_renderer( );

        $fields['live_link'] = array( 
            'type' => 'static',
            'default' => $renderer->div( AMP_TEXT_LIVE_LINK. ': ' . 
                        $renderer->link( $url, $url, 
                                    array( 'target' => 'top')), 
                        array( 'class' => 'preview_link'))
        );
        return $fields;

    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
        
    function _formFooter( ){
        if ( !( $modid = $this->getIdValue( ) )) return false;
        
        require_once( 'AMP/System/IntroText/List.php');
        require_once( 'AMP/System/Tool/Control/List.inc.php');
        require_once( 'AMP/Content/Nav/List.inc.php');
        
        $list_array = array( 
            'Public Pages'  =>  'AMP_System_IntroText_List',
            'Navigation Files'  => 'Nav_List', 
            'Tool Settings' =>  'ToolControl_List');

        $linked_lists = "";
        foreach ( $list_array as $header => $list_class ) {
            $linked_lists .= $this->_outputLinkedList( $list_class, $header );
        }
        return $linked_lists;
    }
    

    function _outputLinkedList( $list_class, $header ){
        if ( !( $modid = $this->getIdValue( ) )) return false;
        $dbcon = &AMP_Registry::getDbcon( );

        $list = &new $list_class( $dbcon, array( 'modid' => $modid )) ;
        //$list->setTool( $modid );
        $list->suppress( 'sort_links');
        $list->suppress( 'messages');
        $list->suppress( 'toolbar');
        $list->drop_column( 'select');

        if ( !$list_html = $list->execute( )) return false;

        require_once( 'AMP/System/Page/Display.inc.php');
        return AMPSystem_Page_Display::makeHeader( $header )
                . $list_html ;

    }
    
}
?>
