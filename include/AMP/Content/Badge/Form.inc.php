<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Badge/ComponentMap.inc.php');

class AMP_Content_Badge_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function AMP_Content_Badge_Form( ) {
        $name = 'badges';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_BADGE );
    }

}
?>
