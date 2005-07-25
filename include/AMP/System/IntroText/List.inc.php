<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'AMP/System/IntroText/Set.inc.php');

class AMPSystem_IntroText_List extends AMPSystem_List {
    var $name = "Intro Text";
    var $col_headers = array(
        'Module Page'=>'name',
        'Module'=>'modid',
        'ID'=>'id');
    var $editlink = 'introtext.php';

    function AMPSystem_IntroText_List( &$dbcon ) {
        $source   =  & new AMPSystem_IntroText_Set($dbcon);
        $this->addLookup( 'modid', $this->getModuleNames() );
        $this->init( $source );
    }

    function getModuleNames () {
        return AMPSystem_Lookup::instance('Modules');
    }
}
?>
