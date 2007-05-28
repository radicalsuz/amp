<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/Tool/Control/Set.inc.php' );
require_once( 'AMP/System/Tool/Control/Control.php' );

class ToolControl_List extends AMPSystem_List {
    var $name = "ToolControl";
    var $col_headers = array( 
        'Control' => 'description',
        'Value'    => 'setting',
        'ID'        => 'id');
    var $editlink = 'tool_controls.php';
    var $_source_object = 'ToolControl';

    function ToolControl_List( &$dbcon, $criteria = array( ) ) {
        $this->init( $this->_init_source( $dbcon, $criteria ));
        //$source = & new ToolControlSet( $dbcon );
        //$this->init( $source );
        $this->addTranslation( 'setting', '_trimSetting');
        $this->suppressAddLink( );
    }

    function setTool( $tool_id ){
        $this->editlink = AMP_Url_AddVars( $this->editlink, 'tool_id='.$tool_id );
        $this->addCriteria( 'modid='.$tool_id);
    }

    function output( ){
        if ( !$this->_prepareData( ))    return false;
        if ( !isset( $this->source ) || !$this->source
             || empty( $this->source ) || ( is_object( $this->source ) && !$this->source->hasData( )))  {
            return false;
        }

        return parent::output( );
    }

    function _trimSetting( $value, $fieldname, $data ){
        return AMP_trimText( $value, 60, false );
    }

    function _init_criteria( $criteria ) {
        if ( isset( $criteria['modid']) && ( $tool_id = $criteria['modid'])) {
            $this->editlink = AMP_url_add_vars( $this->editlink, 'tool_id='.$tool_id );
        }
    }

}
?>
