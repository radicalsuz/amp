<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Quote/ComponentMap.inc.php');

class Quote_Form extends AMPSystem_Form_XML {

    var $name_field = 'quote';

    function Quote_Form( ) {
        $name = 'quotes';
        $this->init( $name );
    }

    function setDynamicValues( ){
       $this->addTranslation( 'date', '_makeDbDateTime', 'get');
    }

    
}
?>
