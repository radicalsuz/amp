<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/Content/Class.inc.php');

class Class_List extends AMPSystem_List {
    var $name = "Class";
    var $col_headers = array( 
        'Class' => 'name',
        'ID'    => 'id',
        'Navigation' => 'navIndex');
    var $editlink = 'class.php';
    var $_source_object = 'ContentClass';

    function Class_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon  ) );
    }

    function navIndex( &$source, $fieldname ){

        $renderer = &$this->_getRenderer( );
        return  $renderer->inDiv( 
                AMP_navCountDisplay_Class( $source->id ),
                    array( 'style' => 'margin:3px;'));

    }

}
?>
