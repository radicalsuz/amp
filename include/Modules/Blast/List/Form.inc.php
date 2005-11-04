<?php

require_once( 'AMP/System/Form/XML.inc.php' );
require_once( 'Modules/Blast/List/ComponentMap.inc.php' );

class BlastList_Form extends AMPSystem_Form_XML {

    function BlastList_Form () {
        $name = "BlastList";
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->setDefaultValue( 'owner', $_SERVER['REMOTE_USER']);
    }

}
?>
