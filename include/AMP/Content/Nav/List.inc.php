<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/Content/Nav/Set.inc.php' );

class Nav_List extends AMPSystem_List {
    var $name = "Nav";
    var $col_headers = array( 
        'Navigation' => 'name',
        'Tool'      =>  'modid',
        'ID'    => 'id');
    var $editlink = AMP_SYSTEM_URL_NAV;
    var $_url_add = AMP_SYSTEM_URL_NAV_ADD;

    function Nav_List( &$dbcon ) {
        $source = & new NavigationSet( $dbcon );
        $this->init( $source );
        $this->addLookup( 'modid', AMPSystem_Lookup::instance( 'tools'));
    }

    function setTool( $tool_id ){
        $this->_url_add = AMP_url_add_vars( AMP_SYSTEM_URL_NAV_ADD, array( 'tool_id='.$tool_id ));
        $this->addCriteria( 'modid='.$tool_id );
    }

}
?>
