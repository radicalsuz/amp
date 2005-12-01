<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/Tool/Control/Set.inc.php' );

class ToolControl_List extends AMPSystem_List {
    var $name = "ToolControl";
    var $col_headers = array( 
        'Control' => 'description',
        'Value'    => 'setting',
        'ID'        => 'id');
    var $editlink = 'tool_controls.php';

    function ToolControl_List( &$dbcon ) {
        $source = & new ToolControlSet( $dbcon );
        $this->init( $source );
        $this->addTranslation( 'setting', '_trimSetting');
        $this->suppressAddLink( );
    }

    function setTool( $tool_id ){
        $this->editlink = AMP_Url_AddVars( $this->editlink, 'tool_id='.$tool_id );
        $this->addCriteria( 'modid='.$tool_id);
    }

    function output( ){
        if ( !$this->_prepareData( ))    return false;
        if ( !$this->source->hasData( )) return false;

        return PARENT::output( );
    }

    function _trimSetting( $value, $fieldname, $data ){
        return AMP_trimText( $value, 60, false );
    }
}
?>
