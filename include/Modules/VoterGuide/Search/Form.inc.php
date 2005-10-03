<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'AMP/Region.inc.php');
require_once ( 'Modules/VoterGuide/Position.php');
require_once ( 'Modules/VoterGuide/ComponentMap.inc.php');

class VoterGuideSearch_Form extends AMPSearchForm {

    var $_component_header = "Search the Guides";

    function VoterGuideSearch_Form (){
        $name = "VoterGuideSearch";
        $this->init( $name );
    }

    function setDynamicValues( ) {
        $region = &new Region( );
        $position = &new VoterGuide_Position( AMP_Registry::getDbcon( ));
        $this->setFieldValueSet( 'state', $region->getSubRegions( 'US' ));
        $this->setFieldValueSet( 'position', $position->getVoteSet( ));
    }

    function getComponentHeader() {
        return $this->_component_header;
    }
}

?>
