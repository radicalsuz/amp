<?php

require_once( 'AMP/Region.inc.php' );
require_once( 'AMP/System/Form/XML.inc.php' );
require_once( 'Modules/VoterGuide/ComponentMap.inc.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'AMP/Form/ElementCopierScript.inc.php' );
require_once( 'Modules/VoterGuide/Position/Form.inc.php' );

class VoterGuide_Form extends AMPSystem_Form_XML {

    var $_positionForm;
    var $_copierName = 'voterguidePositions';
    var $_coreField = 'item';

    function VoterGuide_Form () {
        $name = "VoterGuides";
        $this->init( $name );
    }

    function setDynamicValues() {
        $region = &new Region();
        $this->setFieldValueSet( 'state' , $region->regions['US']);
        $this->addTranslation( 'election_date', '_makeDbDateTime' );
        $this->_showPositions( );
        #$this->addTranslation( 'id', '_loadPositions' );
    }

    function _loadPositions( $data, $fieldname ) {

    }

    function _showPositions() {
        $this->_positionForm = &new VoterGuidePosition_Form();
        $this->_copier = &ElementCopierScript::instance();
        $this->_copier->addCopier( $this->_copierName, $this->_positionForm->getFields(), "VoterGuides" );
        #$this->_copier->setPrefix( $this->_copierName, $this->_field_prefix );
        $this->_copier->setCoreField( $this->_copierName, $this->_coreField );

        if (!empty($_POST)) {
            $this->_copier->addSets( $this->_copierName, $_POST );
        }

        $this->registerJavascript( $this->_copier->output() );
        $this->addFields( $this->_copier->getAddButton( $this->_copierName ) );
    }
        
        
}

?>
