<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Start_Housing extends UserDataPlugin {

    var $available = true;
    var $_housing_form;

    function UserDataPlugin_Save_Housing( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $form = $this->get_form( );
        $this->fields = $this->enable_all( $form->getFields( ));
        $this->insertBeforeFieldOrder( array_keys($this->fields) );

    }

    function get_form( ) {
        if ( isset( $this->_housing_form )) return $this->_housing_form;
        if ( $this->udm->admin ) {
            require_once( 'Modules/Housing/ComponentMap.inc.php');
            $map = new ComponentMap_Housing( );

        } else {
            require_once( 'Modules/Housing/Public/ComponentMap.inc.php');
            $map = new ComponentMap_Housing_Public( );
        }
        $this->_housing_form = $map->getComponent( 'form');
        return $this->_housing_form;
    }

    function execute( $options = array( )) {
        //do nothing
        return true;
    }

}


?>
