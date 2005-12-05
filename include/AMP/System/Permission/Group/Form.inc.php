<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Permission/Group/ComponentMap.inc.php');

class PermissionGroup_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function PermissionGroup_Form( ) {
        $name = 'PermissionGroup';
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->_addSettingsField( );
    }
    function _addSettingsField( ){
        $this->addTranslation( 'settings', '_checkgroupToArray', 'get');
        $this->addTranslation( 'settings', '_checkgroupFromArray', 'set');
    }

}
?>
