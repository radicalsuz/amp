<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Petition/Petition.php');

class Petition_List extends AMPSystem_List {
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

    function Petition_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function publishButton( &$source, $fieldname ){
        return AMP_publicPagePublishButton( $source->id, 'petition_id');
    }

    function _signupLink( &$source, $fieldname ) {
        $form_id = $source->getFormId( );
        if ( !$form_id ) return false;

        $renderer = & $this->_getRenderer( );
        return $renderer->link( 
                    AMP_Url_AddVars( AMP_SYSTEM_URL_FORM_DATA, array( 'modin' => 'modin=' . $form_id )),
                    AMP_TEXT_PETITION_SIGNERS );
    }
}

?>
