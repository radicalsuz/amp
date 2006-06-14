<?php
require_once( 'AMP/System/Observer.php');

class AMP_System_List_Observer extends AMP_System_Observer {

    var $_list;

    function AMP_System_List_Observer( &$list ){
        $this->_list = $list;
    }

    function onDelete( &$source ) {
        $this->_list->removeSourceItemId( $source->id );
    }

    function onUpdate( &$source ){
        $this->_list->updateSourceItemId( $source->id );
    }

    function onReorder( &$source ){
        $this->_list->redoSort( );
    }
}

?>
