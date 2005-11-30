<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( '%4\$s%1\$s/ComponentMap.inc.php');

class %1\$s_Form extends AMPSystem_Form_XML {

    var $name_field = '%3\$s';

    function %1\$s_Form( ) {
        $name = '%2\$s';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here %6\$s auto scaffold items end */
    }
}
?>
