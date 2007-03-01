<?php
require_once( 'AMP/Display/List.php');
require_once( 'Modules/WebAction/Deprecated.php');

class WebAction_Public_List extends AMP_Display_List {
    var $name = 'WebActions';
    var $_source_object = 'WebAction_Deprecated';

    function WebAction_Public_List( $source = false, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }


}

?>
