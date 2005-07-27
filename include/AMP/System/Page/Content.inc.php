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
        if ($action = $this->list->submitted()) {
            if ( $qty = $this->list->doAction( $action ))
                return $this->setMessage( $qty . " items " . AMP_PastParticiple($action) ." succesfully");

            return $this->setMessage("Nothing was " . AMP_PastParticiple( $action ), ( $qty === FALSE ) ); 
        }

        if ( !$action ) $action = $this->default_action;
        $this->doAction( $action );
    }

    function _setSearchFormDefaults() {
        $this->search->applyDefaults();
    }

    function commitSearch() {
        $this->_initComponents( 'list' );
        $this->list->source->applyValues ($this->search->getSearchValues());
        $this->showList( true );
        $this->dropComponent( 'menu' );
    }

    function commitMenu() {
        $this->_initComponents('menu');
        $this->addComponent( 'menu' );
    }


}
?>
