<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Petition/ComponentMap.inc.php');

class Petition_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';

    function Petition_Form( ) {
        $name = 'petition';
        $this->init( $name );
    }

    function setDynamicValues( ){
       $this->addTranslation( 'datestarted', '_makeDbDateTime', 'get');
       $this->addTranslation( 'dateended', '_makeDbDateTime', 'get');
    }
}
?>
