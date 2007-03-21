<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Badge/Badge.php');

class AMP_Content_Badge_List extends AMP_System_List_Form {
    var $name = "Badge";
    var $col_headers = array( 
        'Name' => 'name',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = AMP_SYSTEM_URL_BADGE;
    var $_url_add = AMP_SYSTEM_URL_BADGE_ADD;
    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Badge';
    var $_observers_source = array( 'AMP_System_List_Observer' );

    function AMP_Content_Badge_List( &$dbcon, $criteria = array( ) ) {
        $this->init( $this->_init_source( $dbcon, $criteria ) );
    }
}
?>
