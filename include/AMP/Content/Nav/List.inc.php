<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Nav/Set.inc.php' );
require_once( 'AMP/Content/Nav.inc.php');

class Nav_List extends AMP_System_List_Form {
    var $name = "Nav";
    var $col_headers = array( 
        'Navigation' => 'name',
        'Tool'      =>  'toolname',
        'ID'    => 'id');
    var $editlink = AMP_SYSTEM_URL_NAV;
    var $_url_add = AMP_SYSTEM_URL_NAV_ADD;
    var $_source_object = 'NavigationElement';
    var $_actions = array( 'delete');
    var $name_field = 'name';

    function Nav_List( &$dbcon, $criteria = array( )) {
//        $source = & new NavigationSet( $dbcon );
 //       $this->init( $source );
        $this->init( $this->_init_source( $dbcon, $criteria ) );
        $this->addLookup( 'modid', AMPSystem_Lookup::instance( 'tools'));
    }

    function setTool( $tool_id ){
        $this->_url_add = AMP_url_add_vars( AMP_SYSTEM_URL_NAV_ADD, array( 'tool_id='.$tool_id ));
        $this->addCriteria( 'modid='.$tool_id );
    }

    function _init_criteria( $criteria ) {
        if ( isset( $criteria['modid']) && ( $tool_id = $criteria['modid'])) {
            $this->_url_add = AMP_url_add_vars( AMP_SYSTEM_URL_NAV_ADD, array( 'tool_id='.$tool_id ));
        }
    }

}
?>
