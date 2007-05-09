<?php

require_once( 'AMP/System/List/Form.inc.php' );
require_once( 'AMP/System/IntroText.inc.php');

class AMPSystem_IntroText_List extends AMP_System_List_Form {
    var $name = "Intro Text";
    var $col_headers = array(
        'Page Name'=>'name',
        'Tool'=>'toolName',
        'ID'=>'id',
        'Navigation' => 'navIndex',
        'Publish'  => 'publishButton'
        );
    var $editlink = AMP_SYSTEM_URL_PUBLIC_PAGE;
    var $_url_add = AMP_SYSTEM_URL_PUBLIC_PAGE_ADD;
    var $_source_object = 'AMPSystem_Introtext';
    var $name_field = 'name';
    var $_actions = array( 'delete');

    function AMPSystem_IntroText_List( &$dbcon, $criteria = null ) {
        #$source   =  & new AMPSystem_IntroText_Set($dbcon);
        $this->init( $this->_init_source( $dbcon, $criteria ) );
    }

    function getModuleNames () {
        return AMPSystem_Lookup::instance('Modules');
    }

    function setTool( $tool_id ){
        $forms_list = AMP_lookup( 'formsByTool');
        $url_vars = array( 'tool_id=' . $tool_id );
        if ( isset( $forms_list[ $tool_id ])) $url_vars[] = 'form_id=' . $forms_list[ $tool_id ];

        $this->_url_add = AMP_url_add_vars( AMP_SYSTEM_URL_PUBLIC_PAGE_ADD, $url_vars );
        $this->editlink = AMP_url_add_vars( $this->editlink, $url_vars );

        $this->addCriteria( 'modid='.$tool_id );
    }

    function navIndex( &$source, $fieldname ){

        return AMP_navCountDisplay_Introtext( $source->id );
    }

    function publishButton( &$source, $fieldname ){
        return AMP_publicPagePublishButton( $source->id, 'introtext_id'); 
    }

}
?>
