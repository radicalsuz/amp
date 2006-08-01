<?php

require_once( 'AMP/Region.inc.php' );
require_once( 'AMP/System/Form/XML.inc.php' );
require_once( 'Modules/VoterGuide/ComponentMap.inc.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'AMP/Form/ElementCopierScript.inc.php' );
require_once( 'Modules/VoterGuide/Position/Form.inc.php' );
require_once( 'Modules/VoterGuide/Lookups.inc.php');
require_once ( 'AMP/UserData/Lookups.inc.php');

class VoterGuide_Form extends AMPSystem_Form_XML {

    var $_positionForm;
    var $_copierName = 'voterguidePositions';
    var $_coreField = 'item';

    function VoterGuide_Form () {
        $name = "VoterGuides";
        $this->init( $name );
    }

	function init( $name ) {
		$this->defineSubmit('save', 'Submit My Voter Guide');
		parent::init($name);
	}
		
    function setDynamicValues() {
		$this->setFieldValueSet( 'election_cycle', AMPSystem_Lookup::instance('ElectionCycles'));
		$this->setFieldValueSet( 'style', AMPSystem_Lookup::instance('VoterGuideStyles'));
        $region = &new Region();
        $this->setFieldValueSet( 'state' , $region->regions['US']);
        $this->setFieldValueSet( 'owner_id' , FormLookup_Names::instance( AMP_FORM_ID_VOTERGUIDES ));
        $this->addTranslation( 'election_date', '_makeDbDateTime' );
        $this->showPositions( );
        $this->addTranslation( $this->_copierName, '_loadPositions', 'set' );
    }

    function _loadPositions( $data, $fieldname ) {
        $this->_copier->addRealSets( $this->_copierName, $data );
        $this->registerJavascript( $this->_copier->output(), 'copier' );
        $this->setJavascript();
    }

    function showPositions( ) {
        $this->_positionForm = &new VoterGuidePosition_Form();

        $this->_copier = &ElementCopierScript::instance();
        $this->_copier->addCopier( $this->_copierName, $this->_positionForm->getFields(), "VoterGuides" );
        $this->_copier->setCoreField( $this->_copierName, $this->_coreField );
        $this->_copier->setButtonText( 'Add New Endorsement', 'add', $this->_copierName);
        $this->_copier->setButtonText( 'Remove This Endorsement', 'remove', $this->_copierName);
        $this->_copier->addControlButtons( $this->_copierName );
        
        if (!empty($_POST)) {
            $this->_copier->addSets( $this->_copierName, $_POST );
        }
        
        $this->registerJavascript( $this->_copier->output(), 'copier' );
        
        $this->addFields( $this->_copier->getAddButton( $this->_copierName )  );
    }

    function getValues( $fields=null ) {
        $base_data = parent::getValues( $fields );
        if ( $copier_data = $this->_copier->returnSets(  $this->_copierName ) ) {
            $base_data[ $this->_copierName ] = $copier_data;
        }
        return $base_data;
    }
        
        
}

?>
