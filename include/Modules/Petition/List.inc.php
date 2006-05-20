<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Petition/Petition.php');

class Petition_List extends AMPSystem_List {
    var $name = "Petition";
    var $col_headers = array( 
        'title' => 'name',
        'ID'    => 'id');
    var $editlink = 'petition.php';
    var $name_field = 'title';
    var $_source_object = 'Petition';

    function Petition_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function publishButton( &$source, $fieldname ){
        return AMP_publicPagePublishButton( $source->id, 'petition_id');
    }
}
?>
