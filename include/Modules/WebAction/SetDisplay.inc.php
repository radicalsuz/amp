<?php

require_once ( 'AMP/Content/Display/List.inc.php' );

class WebActionSet_Display extends AMPContent_DisplayList_HTML {

    var $_sourceItem_class = 'WebAction';

    function WebActionSet_Display ( &$source, $read_data = true ) {
        $this->init( $source, $read_data);
    }


    function _HTML_listItemDescription( &$action ) {
        return
            $this->_HTML_listItemTitle( $action ) . 
            $this->_HTML_listItemBlurb( $action->getBlurb() );

    }

    
}

?>
