<?php

require_once( 'AMP/System/List/Toolbar.inc.php');

class AMP_Content_Nav_Location_Toolbar extends AMP_System_List_Toolbar {

    var $_headerContent = array( );

    function AMP_Content_Nav_Location_Toolbar( &$display ){
        $this->_display = &$display;
        $this->submitGroup = 'submitAction' . $display->getName( );
    }


}

?>
