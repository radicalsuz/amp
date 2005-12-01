<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/Content/Nav/Set.inc.php' );

class Nav_List extends AMPSystem_List {
    var $name = "Nav";
    var $col_headers = array( 
        'Navigation' => 'name',
        'Tool'      =>  'modid',
        'ID'    => 'id');
    var $editlink = 'nav_edit.php';

    function Nav_List( &$dbcon ) {
        $source = & new NavigationSet( $dbcon );
        $this->init( $source );
        $this->addLookup( 'modid', AMPSystem_Lookup::instance( 'tools'));
    }

    function setTool( $tool_id ){
        $this->editlink = AMP_Url_AddVars( $this->editlink, 'tool_id='.$tool_id );
        $this->addCriteria( 'modid='.$tool_id );
    }

}
?>
