<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'Modules/VoterGuide/Form.inc.php' );
require_once( 'Modules/UDM/DIA/SupporterSave.inc.php' );

class UserDataPlugin_Save_AMPVoterGuide extends UserDataPlugin_Save {

    var $_field_prefix = 'plugin_AMPVoterGuide';
    var $_guideForm;
    var $_copierName = 'voterguidePositions';
    var $_coreField = 'item';
    var $_copier;

    function UserDataPlugin_Save_AMPVoterGuide ( &$udm, $plugin_instance=null ) {
        $this->init ( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $this->_guideForm = &new VoterGuide_Form();
        $this->fields = $this->_guideForm->getFields();

        $this->_copier = &ElementCopierScript::instance();
        $this->_copier->setFormName( $this->_copierName, $this->udm->name );
        $add_button = key( $this->_copier->getAddButton( $this->_copierName ) );
        unset(  $this->fields[ $add_button ]);

        $this->_copier->setPrefix( $this->_copierName, $this->_field_prefix );
        $add_button = $this->_copier->getAddButton( $this->_copierName );
        $add_button_name = $this->dropPrefix(key( $add_button) );
        $this->fields[ $add_button_name ] =  current( $add_button ) ;

        $fieldnames =  array_keys( $this->fields ) ;
        //$end_form_fieldnames =  array( 'guidePositionsHeader', key( $add_button ) ) ;

//        $this->insertBeforeFieldOrder( array_diff( $fieldnames, $end_form_fieldnames) );
//       $this->insertAfterFieldOrder( $end_form_fieldnames );

        
        if (!empty($_POST)) {
            $this->_copier->addSets( $this->_copierName, $_POST );
        }
        

        $this->_register_javascript( $this->_copier->output() );
    }

    function getSaveFields() {
        return $this->getAllDataFields();
    }


    function save( $data ) {

        $data['owner_id'] = $this->udm->uid;
        if ( $copier_data = $this->_copier->returnSets(  $this->_copierName ) ) {
            $data[ $this->_copierName ] = $copier_data;
        }

        $voterGuide = &new VoterGuide( $this->udm->dbcon );
        $voterGuide->setData( $data );
        if ( $voterGuide->save() ) {
			$organizer_id = $this->udm->doPlugin( 'DIA', 'SupporterSave' );
			$link = $voterGuide->setBlocOrganizer($voterGuide->getData('block_id'), $organizer_id);

			return true;
		}
       
        $this->udm->errorMessage( $voterGuide->getErrors() );
        return false;

    }
}
?>
