<?php

require_once( 'AMP/System/IntroText.inc.php');
require_once( 'AMP/Display/System/List.php');

class AMP_System_Introtext_List extends AMP_Display_System_List {

    var $_source_object = 'AMPSystem_IntroText';
    var $columns = array( 'select', 'edit', 'name', 'tool_name', 'id', 'nav_index', 'publish_button');
    var $column_headers = array( 'nav_index' => 'Navigation', 'publish_button' => 'Publish', 'id' => 'ID');
    var $_actions = array( 'delete' );

    function AMP_System_Introtext_List( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function render_nav_index( &$source ){

        return AMP_navCountDisplay_Introtext( $source->id );
    }

    function render_publish_button( &$source ) {
        return AMP_publicPagePublishButton( $source->id, 'introtext_id'); 
    }

    function setTool( $tool_id ) {
        $this->add_link_var( 'tool_id', $tool_id );
        $forms_list = AMP_lookup( 'formsByTool');
        if ( isset( $forms_list[ $tool_id ])) $this->add_link_var( 'form_id', $forms_list[ $tool_id ]);

    }

    function _init_criteria( ) {
        if (( isset( $this->_source_criteria['modid']) && ( $tool_id = $this->_source_criteria['modid'] )) 
         	|| ( isset( $this->_source_criteria['tool']) && ( $tool_id = $this->_source_criteria['tool'] ))) {
			$this->setTool($tool_id);
/*
            $this->add_link_var( 'tool_id', $tool_id );
            $forms_list = AMP_lookup( 'formsByTool');
            if ( isset( $forms_list[ $tool_id ]) && $forms_list[$tool_id]) {
                $this->add_link_var( 'form_id', $forms_list[ $tool_id ]);
            }
*/
        }

    }

}

?>
