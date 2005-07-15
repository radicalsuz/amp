<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'AMP/System/IntroText.inc.php' );

class AMPSystem_IntroText_List extends AMPSystem_List {
    var $name = "Listing Intro Texts";
    var $datatable = "moduletext";
    var $col_headers = array(
        'Module Page'=>'name',
        'Module'=>'modid',
        'ID'=>'id');
    var $sort = "name";
    var $fields = array(
        'name',
        'modid',
        'id');

    function AMPSystem_IntroText_List( &$dbcon, $fields=null ) {
        $this->dbcon   =  & $dbcon;
        if (isset($fields) ) $this->fields  =  $fields;
        $this->lookups['modid'] = $this->getModuleNames();
        $this->editlink = $_SERVER['PHP_SELF'];
    }

    function getModuleNames () {
        return AMPSystem_Lookup::instance('Modules');
    }
}
?>
