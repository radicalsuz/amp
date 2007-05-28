<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ToolControl extends AMPSystem_Data_Item {

    var $datatable = "module_control";
    var $name_field = "description";
    var $_class_name = 'ToolControl';

    function ToolControl ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function get_url_edit( ) {
        if ( !( isset( $this->id ) && $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_TOOL_CONTROL, array( 'id=' . $this->id ) );
    }

    function makeCriteriaModid( $modid ) {
        return "modid=" . $modid;
    }

    function getDescription( ) {
        return $this->getName( );
    }

    function getSetting( ) {
        return $this->getData( 'setting' );
    }
}

?>
