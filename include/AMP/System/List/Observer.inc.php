<?php
require_once( 'AMP/System/Observer.php');

class AMP_System_List_Observer extends AMP_System_Observer {

    var $_list;

    function AMP_System_List_Observer( &$list ){
        $this->_list = $list;
    }

    function onDelete( &$source ) {
        //$this->_list->removeSourceItemId( $source->id );
        $this->_clear_cache( );
        $this->_reload_page( );
    }

    function onUpdate( &$source ){
        //$this->_list->updateSourceItemId( $source->id );
        $this->_reload_page( );
        $this->_clear_cache( );
    }

    function onReorder( &$source ){
    //    $this->_list->redoSort( );
        $this->_reload_page( );
        $this->_clear_cache( );
    }

    function _reload_page( ){
        ampredirect( $_SERVER['REQUEST_URI']);
    }

    function _clear_cache( ){
        $dbcon = &AMP_Registry::getDbcon( );
        $dbcon->CacheFlush( );
    }

    function onMove( &$source ) {
        $this->_reload_page( );
        $this->_clear_cache( );

    }
}

?>
