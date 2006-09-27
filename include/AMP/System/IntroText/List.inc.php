<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'AMP/System/IntroText/Set.inc.php');
require_once( 'AMP/System/IntroText.inc.php');

class AMPSystem_IntroText_List extends AMPSystem_List {
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

    function AMPSystem_IntroText_List( &$dbcon ) {
        #$source   =  & new AMPSystem_IntroText_Set($dbcon);
        $this->init( $this->_init_source( $dbcon ) );
    }

    function getModuleNames () {
        return AMPSystem_Lookup::instance('Modules');
    }

    function setTool( $tool_id ){
        $this->_url_add = AMP_url_add_vars( AMP_SYSTEM_URL_PUBLIC_PAGE_ADD, array( 'tool_id='.$tool_id ) );
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
