<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'Modules/VoterGuide/Form.inc.php' );
require_once( 'Modules/UDM/DIA/SupporterSave.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );

class UserDataPlugin_Save_AMPVoterGuide extends UserDataPlugin_Save {

	var $short_name = 'Save_AMPVoterGuide';
	var $long_name = 'Save';
    var $name = 'Save Voter Guide Data';
    var $description = 'Save voter guide data into the AMP database';
	var $available = true;

	var $options = array(
		'dia_parent_group' => array(
			'description'=>'group under which voter guides created from this form will be stored',
			'label' => 'DIA Parent Group',
			'name'=>'Parent Group',
			'type'=>'text',
			'available'=>true),
		'election_cycle' => array(
			'description' => 'unique election cycle',
			'label' => 'Election Cycle',
			'name' => 'Election Cycle',
			'type' => 'text',
			'available' => true),
		'election_date' => array(
			'description' => 'election date',
			'label' => 'Election Date',
			'name' => 'Election Date',
			'type' => 'date',
			'available' => true)
		);

    var $_field_prefix = 'plugin_AMPVoterGuide';
    var $_guideForm;
    var $_copierName = 'voterguidePositions';
    var $_coreField = 'item';
    var $_copier;

    function UserDataPlugin_Save_AMPVoterGuide ( &$udm, $plugin_instance=null ) {
        $this->init ( $udm, $plugin_instance );
		$callback = array('callback' => array(&$this, 'invalidForm'));
        $udm->addFormCallback('AMP_UDM_FORM_INVALID', $callback);
    }

	function _register_options_dynamic() {
//		$this->options['election_date']['default']
	}

    function _register_fields_dynamic() {
        $this->_guideForm = &new VoterGuide_Form();
        $this->fields = $this->_guideForm->getFields();
		$current = getdate();
		$this->fields['election_date']['options']['minYear'] = $current['year'];

        $this->_copier = &ElementCopierScript::instance();
        $this->_copier->setFormName( $this->_copierName, $this->udm->name );

        //Add Button has to be reconfigured for UDM
        $add_button = key( $this->_copier->getAddButton( $this->_copierName ) );
        unset(  $this->fields[ $add_button ]);
        unset(  $this->fields[ 'owner_id' ]);

        $this->_copier->setPrefix( $this->_copierName, ( $this->_field_prefix . '_' . $this->_copierName ));
        $add_button = $this->_copier->getAddButton( $this->_copierName );
        $add_button_name = $this->dropPrefix(key( $add_button) );
        $this->fields[ $add_button_name ] =  current( $add_button ) ;
        
        //Position ID field has to be reconfigured for udm

        if(defined('AMP_VOTERGUIDE_SUBMIT_TEXT')) {
            $this->fields['submit_text'] = array(
                                'type' => 'static',
                                'enabled' => true,
                                'public' => true,
                                'required' => false,
                                'values' => AMP_VOTERGUIDE_SUBMIT_TEXT);
        }

        $this->insertAfterFieldOrder( array_keys( $this->fields ));



        
        if (!empty($_POST)) {
            $this->_copier->addSets( $this->_copierName, $_POST );
        }
        

        $this->_register_javascript( $this->_copier->output() );
    }

    function getSaveFields() {
        return $this->getAllDataFields();
    }


    function save( $data, $options=null ) {
        $options = array_merge( $this->getOptions(), $options );

        $data['owner_id'] = $this->udm->uid;
        if ( $copier_data = $this->_copier->returnSets(  $this->_copierName ) ) {
            $data[ $this->_copierName ] = $copier_data;
        }


        $voterGuide = &new VoterGuide( $this->udm->dbcon );
        $voterGuide->setData( $data );
		if(isset($options['dia_parent_group'])) {
			$voterGuide->setParentGroup($options['dia_parent_group']);
		}

		if(isset($options['election_cycle']) && !$voterGuide->getData('election_cycle')) {
			$voterGuide->mergeData(array('election_cycle'=>$options['election_cycle']));
		}
        if ( $voterGuide->save() ) {
			$organizer_id = $this->udm->tryPlugin( 'DIA', 'SupporterSave' );
			$link = $voterGuide->setBlocOrganizer($voterGuide->getBlocID(), $organizer_id);

			$currentPage =& AMPContent_Page::instance();
			$currentPage->addObject(strtolower(__CLASS__), $voterGuide);
			return true;
		}
       
        $this->udm->errorMessage( $voterGuide->getErrors() );

		$customErrors = $voterGuide->getCustomErrors();

		if($customErrors) {
			foreach($customErrors as $error) {
				$field = $this->_field_prefix.'_'.$error['field'];
				$this->udm->setErrorHandler($field, array(&$this->udm->form, 'setElementError'));
				$this->udm->addError($field, array($field,$error['message']));
//				ok, so it turns out this is overkill right now,
//				but i really like this customErrorHandler, so there.
//				also, now all error output is handled in the same place
//				$this->customErrorHandler($error[0], $error[1]);
			}
		}

        return false;

    }

    function invalidForm() {
		if(isset($_FILES[$this->_field_prefix.'_'.'filelink']) && $_FILES[$this->_field_prefix.'_'.'filelink']['name']) {
			$this->udm->form->setElementError($this->_field_prefix.'_'.'filelink', 'You will need to select your file again');
		}
		if(isset($_FILES[$this->_field_prefix.'_'.'picture']) && $_FILES[$this->_field_prefix.'_'.'picture']['name']) {
			$this->udm->form->setElementError($this->_field_prefix.'_'.'picture', 'You will need to select your file again');
		}

		if(!empty($_POST) && !$_POST[$this->_field_prefix.'_'.'accurate_confirm']) {
			$this->udm->form->setElementError($this->_field_prefix.'_'.'accurate_confirm', 'This is a required field');
		}
		if(!empty($_POST) && !$_POST[$this->_field_prefix.'_'.'trust_confirm']) {
			$this->udm->form->setElementError($this->_field_prefix.'_'.'trust_confirm', 'This is a required field');
		}
    } 
 
/*
	function customErrorHandler($field, $message) {
		$this->udm->form->setElementError($this->_field_prefix.'_'.$field, $message);
	}
*/
}
?>
