<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'Modules/VoterGuide/Form.inc.php' );

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

        $fieldnames =  array_keys( $this->fields ) ;
        $end_form_fieldnames =  array('guidePositionsHeader', 'add_voterguidePositions') ;
        if ($header_key = array_search( 'guidePositionsHeader', $fieldnames )) {
            unset( $fieldnames [ $header_key ] );
        }
        $this->insertBeforeFieldOrder( $fieldnames );
        $this->insertAfterFieldOrder( $end_form_fieldnames );

        $this->_copier = &ElementCopierScript::instance();
        $this->_copier->setFormName( $this->_copierName, $this->udm->name );
        $this->_copier->setPrefix( $this->_copierName, $this->_field_prefix );
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

        $voterGuide = &new VoterGuide( $this->udm->dbcon );
        $voterGuide->setData( $data );
        if ( ! $voterGuide->save() ) {
            $this->udm->errorMessage( 'VoterGuide Save Failed' );
            return false;
        }
        
        return $this->udm->doPlugin( 'AMPVoterGuide', 'PositionSave', array( 'voterguide_id' => $voterGuide->id ) );
    }

}
?>
