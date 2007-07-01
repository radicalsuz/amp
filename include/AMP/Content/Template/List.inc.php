<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/Content/Template.inc.php');

class AMP_Content_Template_List extends AMP_Display_System_List {
    var $columns = array( 'controls', 'name', 'id');
    var $name_field = 'name';
    var $_source_object = 'AMPContent_Template';
    var $_suppress_toolbar = true;

    function AMP_Content_Template_List( $source = false, $criteria = array( ) ) {
        $source = false;
        $this->__construct( $source, $criteria );
    }
}
?>
