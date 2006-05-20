<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/Content/Hotword/Hotword.php');

class Hotword_List extends AMPSystem_List {
    var $name = "Hotword";
    var $col_headers = array( 
        'Hot Word' => 'name',
        'Target' => 'URL',
        'Status' => 'statusText',
        'ID'    => 'id');
    var $editlink = 'hotwords.php';
    var $name_field = 'word';
    var $_source_object = 'Hotword';

    function Hotword_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}

?>
