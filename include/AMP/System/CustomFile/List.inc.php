<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/System/CustomFile/CustomFile.php');

class AMP_System_CustomFile_List extends AMP_System_List_Form {
    var $col_headers = array( 
        'File Name' => 'name',
        'Last Edited' => 'time');

    var $_source_object = 'AMP_System_CustomFile';
    var $editlink = AMP_SYSTEM_URL_CUSTOM_FILE;
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_actions = array( 'delete' );
    var $_url_add = 'customfile.php?action=new';

    function AMP_System_CustomFile_List( ) {
        $listSource = &new $this->_source_object( );
        $source = & $listSource->search( );
        $this->init( $source );
        $this->addTranslation( 'time', '_makePrettyDateTime');
    }

    function _getSourceRow( ) {
        $row_data = parent::_getSourceRow( );
        if ( $row_data ) $row_data['id'] = $row_data['name'];
        return $row_data;
    }
}

?>
