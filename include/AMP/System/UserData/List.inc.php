<?php
require_once( 'AMP/System/List/Form.inc.php');

class AMP_System_UserData_List extends AMP_System_List_Form {
    var $_url_add = 'form_manager.php?action=add';
    var $editlink = AMP_SYSTEM_URL_FORM_SETUP;
//    var $suppress = array( 'header'=>true , 'editcolumn' =>true );
    var $col_headers = array( 
        'Form Name' => 'name',
        'Data Status' => 'publish',
        'Data' => '_dataActions',
        'ID' => 'id',
    );
    var $name_field = 'name';

    var $_source_object = 'AMPSystem_UserData';
    var $_observers_source = array( 'AMP_System_List_Observer' );

    function AMP_System_UserData_List( &$dbcon ){
        $this->init( $this->_init_source( $dbcon ));
    }

    function _getUrlEdit( $row_data ){
        return AMP_Url_AddVars( $this->editlink, "modin=".$row_data['id']);
    }

    function _dataActions( &$source, $fieldname ){
        $renderer = &$this->_getRenderer( );
        $list_url = AMP_URL_AddVars( AMP_SYSTEM_URL_FORM_DATA, array( 'modin='.$source->id));
        $form_url = AMP_URL_AddVars( AMP_SYSTEM_URL_FORM_ENTRY, array( 'modin='.$source->id));
        $import_url = AMP_URL_AddVars( AMP_SYSTEM_URL_FORMS, array( 'modin='.$source->id, 'action=upload'));
        return 
            $renderer->link( $list_url, AMP_TEXT_VIEW )
            . $renderer->space( 2 )
            . $renderer->link( $form_url, AMP_TEXT_ADD )
            . $renderer->space( 2 )
            . $renderer->link( $import_url, AMP_TEXT_IMPORT );
    }

}

?>
