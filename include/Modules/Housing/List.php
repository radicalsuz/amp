<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Housing/Post.php');

class Housing_List extends AMP_System_List_Form {
    var $name = 'Housing';
    var $col_headers = array( 
        'id' => 'id',
        'Type' => 'type',
        'Location' => 'location',
        'Name' => 'name'
    );

    var $editlink = AMP_SYSTEM_URL_HOUSING;
    var $_url_add = AMP_SYSTEM_URL_EVENT_ADD;

    var $name_field = 'name';
    var $_source_object = 'Housing_Post';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function Housing_List( &$dbon, $criteria = array( )) {
        $this->init( $this->_init_source( $dbon, $criteria ));
    }
}


?>
