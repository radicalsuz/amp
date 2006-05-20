<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Template/ComponentMap.inc.php');

class AMP_Content_Template_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function AMP_Content_Template_Form( ) {
        $name = 'template';
        $this->init( $name );
    }

}
?>
