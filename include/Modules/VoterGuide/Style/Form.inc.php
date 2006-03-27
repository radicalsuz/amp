<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/VoterGuide/Style/ComponentMap.inc.php');

class VoterGuide_Style_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function VoterGuide_Style_Form( ) {
        $name = 'voterguide_styles';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
