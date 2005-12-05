<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Permission/Detail/ComponentMap.inc.php');

class PermissionDetail_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function PermissionDetail_Form( ) {
        $name = 'per_description';
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'groups', '_checkgroupToArray', 'get');
        $this->addTranslation( 'groups', '_checkgroupFromArray', 'set');
    }
}
?>
