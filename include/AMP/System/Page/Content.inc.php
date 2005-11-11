<?php

require_once ('AMP/System/Page.inc.php' );

class AMPSystem_Page_Content extends AMPSystem_Page {

    var $default_action = "list";

    function AMPSystem_Page_Content ( &$dbcon, $component_map=null ) {
        $this->init ( $dbcon, $component_map );
    }

    function execute() {
        $this->_initComponents ( "search" );
        $this->search->Build( true );
        if ($action = $this->search->submitted() ) $this->doAction( $action );
        else $this->_setSearchFormDefaults();
        
        $this->_initComponents( "list" );
        if ($list_action = $this->list->submitted()) {
            if ( $qty = $this->list->doAction( $list_action ))
                return $this->setMessage( $qty . " items " . AMP_PastParticiple($list_action) ." succesfully");

            return $this->setMessage("Nothing was " . AMP_PastParticiple( $list_action ), ( $qty === FALSE ) ); 
        }

        if ( !$action ) $this->doAction( $this->default_action );
    }


    function commitMenu() {
        $this->_initComponents('menu');
        $this->addComponent( 'menu' );
    }

    function commitSearch( ){
        PARENT::commitSearch( );
        $this->dropComponent( 'menu' );
    }


}
?>
