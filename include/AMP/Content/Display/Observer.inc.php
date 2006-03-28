<?php
require_once( 'AMP/System/Observer.php');

class AMP_Content_Display_Observer extends AMP_System_Observer {
    var $_list;

    function AMP_Content_Display_Observer( &$list ){
        //interface
        $this->_list = $list;
    }

    function onDelete( &$source ) {
        $this->_list->removeSourceItemId( $source->id );
    }

    function onUpdate( &$source ){
        $this->_list->updateSourceItemId( $source->id );
    }
}

?>
