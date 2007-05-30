<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Display/System/List.php');
require_once( 'Modules/Petition/Petition.php');

//class Petition_List extends AMP_System_List_Form {
class Petition_List extends AMP_Display_System_List {
    var $name = "Petition";
    var $col_headers = array( 
        'title' => 'name',
        'ID'    => 'id',
        'signers' => '_signupLink',
        'publish' => 'publishButton'
        );
    var $editlink = AMP_SYSTEM_URL_PETITIONS;
    var $_url_add = 'petition.php?action=add' ;
    var $name_field = 'title';
    var $_source_object = 'Petition';
    var $link_list_preview= AMP_CONTENT_URL_PETITIONS;
    var $columns = array( 'select', 'edit', 'preview', 'name', 'id', 'signers', 'publish');
    var $column_headers = array( 'preview' => '', 'id' => 'ID');

    function Petition_List( &$source, $criteria = array( ) ) {
        $this->__construct( $this->_init_source( $source, $criteria ) );
    }

    function publishButton( &$source, $fieldname=null ){
        return AMP_publicPagePublishButton( $source->id, 'petition_id');
    }

    function render_publish( $source ) {
        return $this->publishButton( $source );
    }

    function render_signers( $source ) {
        return $this->_signupLink( $source );
    }

    function _signupLink( &$source, $fieldname=null ) {
        $form_id = $source->getFormId( );
        if ( !$form_id ) return false;

        $renderer = & $this->_getRenderer( );
        return $renderer->link( 
                    AMP_Url_AddVars( AMP_SYSTEM_URL_FORM_DATA, array( 'modin' => 'modin=' . $form_id )),
                    AMP_TEXT_PETITION_SIGNERS );
    }

}
?>
