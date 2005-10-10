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

	function init( $name ) {
		if(!defined('AMP_FORM_ELEMENT_COPIER_ADD_BUTTON')) {
			define('AMP_FORM_ELEMENT_COPIER_ADD_BUTTON', 'Add New Endorsement');
		}
		if(!defined('AMP_FORM_ELEMENT_COPIER_VALUE_ARRAY_DEFAULT')) {
			define('AMP_FORM_ELEMENT_COPIER_VALUE_ARRAY_DEFAULT', 'Select One');
		}
		if(defined('AMP_FORM_ELEMENT_COPIER_REMOVE_BUTTON')) {
			define('AMP_FORM_ELEMENT_COPIER_REMOVE_BUTTON', 'Remove This Endorsement');
		}
		$this->defineSubmit('save', 'Submit My Voter Guide');
		parent::init($name);
//		AMP_varDump($result);
//        $this->form->addRule('plugin_AMPVoterGuide_short_name', 'BLAH!', 'callback', 'blah', 'VoterGuide_Form');
	}
		
    function setDynamicValues() {
        $region = &new Region();
        $this->setFieldValueSet( 'state' , $region->regions['US']);
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
        
        if (!empty($_POST)) {
            $this->_copier->addSets( $this->_copierName, $_POST );
        }
        
        $this->registerJavascript( $this->_copier->output(), 'copier' );
        
        $this->addFields( $this->_copier->getAddButton( $this->_copierName )  );
    }

    function getValues( $fields=null ) {
        $base_data = PARENT::getValues( $fields );
        if ( $copier_data = $this->_copier->returnSets(  $this->_copierName ) ) {
            $base_data[ $this->_copierName ] = $copier_data;
        }
        return $base_data;
    }
        
        
}

?>
